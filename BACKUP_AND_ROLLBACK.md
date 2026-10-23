# VSP TIMES E-Paper — Backup & Rollback Runbook

This document details disaster recovery, daily database backups, PDF storage preservation, and release rollback procedures for `https://epaper.vspmedianetwork.com/`.

---

## 1. Backup Strategy

### A. Daily MySQL Database Backup
Set up a daily cron at 01:00 AM IST to back up the database:
```bash
#!/bin/bash
BACKUP_DIR="/home/u123456789/backups/epaper/db"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
mkdir -p "$BACKUP_DIR"

# Dump database
mysqldump -u u123456789_epaperuser -p'DATABASE_PASSWORD' u123456789_epaper | gzip > "$BACKUP_DIR/epaper_db_$TIMESTAMP.sql.gz"

# Retain backups for 30 days
find "$BACKUP_DIR" -type f -name "*.sql.gz" -mtime +30 -exec rm {} \;
```

### B. Master PDF Archival Storage Backup
The master newspaper PDFs stored in `storage/app/epaper/staging/` should be synchronized weekly to external offsite storage (e.g. AWS S3, Google Drive, or secondary VPS):
```bash
rsync -avz --delete /home/u123456789/domains/epaper.vspmedianetwork.com/storage/app/epaper/ /home/u123456789/backups/epaper/pdf_staging/
```

---

## 2. Emergency Rollback Procedures

### Scenario A: Bad Code Deployment
If a new release causes errors or performance issues:

1. **Activate Maintenance Mode:**
   ```bash
   php artisan down --secret="vsp-emergency-bypass-token" --render="errors::503"
   ```

2. **Revert Git to Last Stable Commit:**
   ```bash
   git log -n 5 --oneline
   git checkout <PREVIOUS_STABLE_COMMIT_HASH>
   ```

3. **Re-optimize Application:**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm run build
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Deactivate Maintenance Mode:**
   ```bash
   php artisan up
   ```

---

### Scenario B: Accidental Edition Publication or Corrupt PDF
If an incorrect edition was published or a broken PDF was released:

1. **Unpublish or Change Status:**
   * Go to `https://epaper.vspmedianetwork.com/admin/editions`
   * Select the affected edition and click **సవరించండి (Edit)**
   * Change status to **Draft** or **Soft Delete**
   * The edition immediately disappears from public homepage and archive.
2. **Or via Artisan Tinker:**
   ```bash
   php artisan tinker --execute="App\Models\EpaperEdition::where('id', <ID>)->update(['status' => 'draft']);"
   ```

---

### Scenario C: Database Restoration
To restore a database snapshot:
```bash
gunzip < /home/u123456789/backups/epaper/db/epaper_db_YYYYMMDD_HHMMSS.sql.gz | mysql -u u123456789_epaperuser -p'DATABASE_PASSWORD' u123456789_epaper
php artisan optimize:clear
```
