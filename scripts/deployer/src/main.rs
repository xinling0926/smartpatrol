//! SmartPatrol Auto-Deployment Tool
//!
//! This tool monitors a flag file and triggers git pull deployment
//! when the flag is present. All deployment logs are stored in MySQL.

use chrono::Local;
use mysql::prelude::*;
use mysql::*;
use serde::Deserialize;
use std::fs;
use std::path::Path;
use std::process::Command;

/// Configuration structure loaded from config.toml
#[derive(Debug, Deserialize)]
struct Config {
    deploy: DeployConfig,
    database: DatabaseConfig,
}

#[derive(Debug, Deserialize)]
struct DeployConfig {
    /// Path to the flag file that triggers deployment
    flag_file: String,
    /// Repository directory to deploy
    repo_dir: String,
    /// Git branch to pull from
    branch: String,
    /// Apache user for file ownership
    web_user: String,
    /// Apache group for file ownership
    web_group: String,
}

#[derive(Debug, Deserialize)]
struct DatabaseConfig {
    host: String,
    port: u16,
    user: String,
    password: String,
    database: String,
}

/// Deployment status enum
#[derive(Debug, Clone, Copy)]
enum DeployStatus {
    Success = 1,
    Failed = 0,
}

impl std::fmt::Display for DeployStatus {
    fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
        match self {
            DeployStatus::Success => write!(f, "SUCCESS"),
            DeployStatus::Failed => write!(f, "FAILED"),
        }
    }
}

/// Log entry structure for database
struct DeployLog {
    status: DeployStatus,
    commit_hash: String,
    commit_message: String,
    git_output: String,
    error_message: String,
}

fn main() {
    let config = match load_config() {
        Ok(c) => c,
        Err(e) => {
            eprintln!("Failed to load config: {}", e);
            std::process::exit(1);
        }
    };

    // Check if flag file exists
    if !Path::new(&config.deploy.flag_file).exists() {
        // No deployment needed, exit silently
        return;
    }

    println!(
        "[{}] Deploy triggered - flag file found",
        Local::now().format("%Y-%m-%d %H:%M:%S")
    );

    // Remove flag file first to prevent duplicate runs
    if let Err(e) = fs::remove_file(&config.deploy.flag_file) {
        eprintln!("Warning: Failed to remove flag file: {}", e);
    }

    // Get current commit hash before pull
    let before_hash = get_current_commit(&config.deploy.repo_dir).unwrap_or_default();

    // Execute git pull
    let (git_success, git_output) = execute_git_pull(&config.deploy.repo_dir, &config.deploy.branch);

    // Get new commit hash and message after pull
    let after_hash = get_current_commit(&config.deploy.repo_dir).unwrap_or_default();
    let commit_message = get_commit_message(&config.deploy.repo_dir).unwrap_or_default();

    // Set file permissions
    let perm_result = set_permissions(&config.deploy);

    // Determine final status
    let (status, error_message) = if git_success && perm_result.is_ok() {
        (DeployStatus::Success, String::new())
    } else {
        let mut errors = Vec::new();
        if !git_success {
            errors.push(format!("Git pull failed: {}", git_output));
        }
        if let Err(e) = perm_result {
            errors.push(format!("Permission error: {}", e));
        }
        (DeployStatus::Failed, errors.join("; "))
    };

    // Create log entry
    let log = DeployLog {
        status,
        commit_hash: after_hash.clone(),
        commit_message,
        git_output: git_output.clone(),
        error_message,
    };

    // Save to database
    if let Err(e) = save_log_to_db(&config.database, &log) {
        eprintln!("Failed to save log to database: {}", e);
        // Also write to file as backup
        write_backup_log(&log);
    }

    // Print result
    println!(
        "[{}] Deploy {} - {} -> {}",
        Local::now().format("%Y-%m-%d %H:%M:%S"),
        log.status,
        &before_hash[..7.min(before_hash.len())],
        &log.commit_hash[..7.min(log.commit_hash.len())]
    );

    if !log.error_message.is_empty() {
        eprintln!("Errors: {}", log.error_message);
    }
}

/// Load configuration from config.toml
fn load_config() -> Result<Config, Box<dyn std::error::Error>> {
    // Try to load from environment variable first
    let config_path = std::env::var("DEPLOYER_CONFIG")
        .unwrap_or_else(|_| "/etc/smartpatrol/deployer.toml".to_string());

    let content = fs::read_to_string(&config_path)
        .map_err(|e| format!("Cannot read config file {}: {}", config_path, e))?;

    let config: Config = toml::from_str(&content)
        .map_err(|e| format!("Invalid config format: {}", e))?;

    Ok(config)
}

/// Get current commit hash
fn get_current_commit(repo_dir: &str) -> Option<String> {
    let output = Command::new("git")
        .args(["rev-parse", "HEAD"])
        .current_dir(repo_dir)
        .output()
        .ok()?;

    if output.status.success() {
        Some(String::from_utf8_lossy(&output.stdout).trim().to_string())
    } else {
        None
    }
}

/// Get latest commit message
fn get_commit_message(repo_dir: &str) -> Option<String> {
    let output = Command::new("git")
        .args(["log", "-1", "--pretty=%s"])
        .current_dir(repo_dir)
        .output()
        .ok()?;

    if output.status.success() {
        Some(String::from_utf8_lossy(&output.stdout).trim().to_string())
    } else {
        None
    }
}

/// Execute git pull and return (success, output)
fn execute_git_pull(repo_dir: &str, branch: &str) -> (bool, String) {
    let output = Command::new("git")
        .args(["pull", "origin", branch])
        .current_dir(repo_dir)
        .output();

    match output {
        Ok(out) => {
            let stdout = String::from_utf8_lossy(&out.stdout);
            let stderr = String::from_utf8_lossy(&out.stderr);
            let combined = format!("{}{}", stdout, stderr);
            (out.status.success(), combined.trim().to_string())
        }
        Err(e) => (false, format!("Failed to execute git: {}", e)),
    }
}

/// Set file permissions for writable directory
fn set_permissions(config: &DeployConfig) -> Result<(), String> {
    let writable_dir = format!("{}/writable", config.repo_dir);

    let output = Command::new("chown")
        .args([
            "-R",
            &format!("{}:{}", config.web_user, config.web_group),
            &writable_dir,
        ])
        .output();

    match output {
        Ok(out) if out.status.success() => Ok(()),
        Ok(out) => Err(String::from_utf8_lossy(&out.stderr).to_string()),
        Err(e) => Err(e.to_string()),
    }
}

/// Save deployment log to MySQL database
fn save_log_to_db(db_config: &DatabaseConfig, log: &DeployLog) -> Result<(), Box<dyn std::error::Error>> {
    let url = format!(
        "mysql://{}:{}@{}:{}/{}",
        db_config.user, db_config.password, db_config.host, db_config.port, db_config.database
    );

    let pool = Pool::new(url.as_str())?;
    let mut conn = pool.get_conn()?;

    // Create table if not exists
    conn.query_drop(
        r"CREATE TABLE IF NOT EXISTS deploy_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            deploy_time DATETIME NOT NULL,
            status TINYINT NOT NULL COMMENT '0=failed, 1=success',
            commit_hash VARCHAR(40),
            commit_message VARCHAR(500),
            git_output TEXT,
            error_message TEXT,
            INDEX idx_deploy_time (deploy_time),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    )?;

    // Insert log entry
    conn.exec_drop(
        r"INSERT INTO deploy_log (deploy_time, status, commit_hash, commit_message, git_output, error_message)
          VALUES (NOW(), :status, :hash, :msg, :output, :error)",
        params! {
            "status" => log.status as i32,
            "hash" => &log.commit_hash,
            "msg" => &log.commit_message,
            "output" => &log.git_output,
            "error" => &log.error_message,
        },
    )?;

    Ok(())
}

/// Write backup log to file if database fails
fn write_backup_log(log: &DeployLog) {
    let backup_file = "/var/log/smartpatrol/deploy_backup.log";

    // Try to create directory if it doesn't exist
    if let Some(parent) = Path::new(backup_file).parent() {
        let _ = fs::create_dir_all(parent);
    }

    let log_line = format!(
        "{} | {} | {} | {} | {}\n",
        Local::now().format("%Y-%m-%d %H:%M:%S"),
        log.status,
        log.commit_hash,
        log.commit_message,
        log.error_message
    );

    if let Err(e) = fs::OpenOptions::new()
        .create(true)
        .append(true)
        .open(backup_file)
        .and_then(|mut f| std::io::Write::write_all(&mut f, log_line.as_bytes()))
    {
        eprintln!("Failed to write backup log: {}", e);
    }
}
