# ============================================================
# Source Code Export to PDF-Ready HTML
# Project: Community App (FYP)
# Run this script from the FYP folder
# ============================================================

$projectPath = "C:\Users\Asus\Documents\GitHub\dg_fyp\FYP\comunity-app"
$outputFile  = "C:\Users\Asus\Documents\GitHub\dg_fyp\FYP\source_code_export.html"

# File extensions to include
$extensions = @("*.php", "*.blade.php", "*.js", "*.css", "*.json", "*.env.example", "*.sql")

# Folders to EXCLUDE
$excludeFolders = @("vendor", "node_modules", ".git", "storage", "bootstrap\cache", "public\build")

Write-Host "Scanning project files..." -ForegroundColor Cyan

# Collect files
$files = Get-ChildItem -Path $projectPath -Recurse -Include $extensions -File | Where-Object {
    $path = $_.FullName
    $excluded = $false
    foreach ($folder in $excludeFolders) {
        if ($path -like "*\$folder\*") {
            $excluded = $true
            break
        }
    }
    -not $excluded
} | Sort-Object FullName

Write-Host "Found $($files.Count) files." -ForegroundColor Green

# HTML escape function
function HtmlEscape($text) {
    $text = $text -replace '&', '&amp;'
    $text = $text -replace '<', '&lt;'
    $text = $text -replace '>', '&gt;'
    $text = $text -replace '"', '&quot;'
    return $text
}

# Build HTML
$html = @"
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Source Code - Community App FYP</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Inter:wght@400;600;700&display=swap');

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', sans-serif;
    background: #fff;
    color: #1a1a2e;
    font-size: 9pt;
  }

  /* Cover Page */
  .cover {
    width: 100%; height: 100vh;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    page-break-after: always;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    color: white; text-align: center; padding: 40px;
  }
  .cover h1 { font-size: 28pt; font-weight: 700; margin-bottom: 12px; }
  .cover h2 { font-size: 16pt; font-weight: 400; opacity: 0.8; margin-bottom: 24px; }
  .cover .meta { font-size: 10pt; opacity: 0.6; line-height: 1.8; }
  .cover .accent { width: 60px; height: 4px; background: #e94560; margin: 20px auto; border-radius: 2px; }

  /* Table of Contents */
  .toc {
    padding: 40px 60px;
    page-break-after: always;
  }
  .toc h2 { font-size: 18pt; font-weight: 700; margin-bottom: 24px; color: #0f3460; border-bottom: 2px solid #e94560; padding-bottom: 8px; }
  .toc ol { padding-left: 20px; }
  .toc li { padding: 3px 0; font-size: 9pt; color: #333; line-height: 1.6; }
  .toc li span { color: #888; font-size: 8pt; margin-left: 8px; }

  /* File Section */
  .file-section {
    padding: 20px 40px 10px 40px;
    page-break-inside: avoid;
    page-break-before: always;
  }

  .file-header {
    background: #0f3460;
    color: white;
    padding: 8px 14px;
    border-radius: 6px 6px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0;
  }
  .file-header .file-path { font-size: 8pt; opacity: 0.8; }
  .file-header .file-name { font-size: 10pt; font-weight: 700; }
  .file-header .file-ext {
    background: #e94560;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 7pt;
    font-weight: 600;
    text-transform: uppercase;
  }

  pre {
    background: #f8f9fc;
    border: 1px solid #e0e4ef;
    border-top: none;
    border-radius: 0 0 6px 6px;
    padding: 12px 14px;
    font-family: 'JetBrains Mono', 'Courier New', monospace;
    font-size: 7.5pt;
    line-height: 1.55;
    overflow-x: auto;
    white-space: pre-wrap;
    word-wrap: break-word;
    color: #2d3748;
    counter-reset: line;
  }

  .line {
    display: block;
    counter-increment: line;
  }
  .line::before {
    content: counter(line);
    display: inline-block;
    width: 32px;
    color: #a0aec0;
    text-align: right;
    margin-right: 14px;
    font-size: 6.5pt;
    user-select: none;
    border-right: 1px solid #dde;
    padding-right: 8px;
  }

  /* Print Settings */
  @media print {
    body { font-size: 8pt; }
    .cover { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .file-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    pre { font-size: 6.5pt; background: #f8f9fc !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .file-section { page-break-before: always; }
  }
</style>
</head>
<body>

<!-- COVER PAGE -->
<div class="cover">
  <h1>Community App</h1>
  <h2>Final Year Project — Source Code</h2>
  <div class="accent"></div>
  <div class="meta">
    <div>Generated: $(Get-Date -Format "dd MMMM yyyy, hh:mm tt")</div>
    <div>Total Files: $($files.Count)</div>
    <div>Project Path: comunity-app/</div>
  </div>
</div>

<!-- TABLE OF CONTENTS -->
<div class="toc">
  <h2>Table of Contents</h2>
  <ol>
"@

# Add TOC entries
$i = 1
foreach ($file in $files) {
    $relativePath = $file.FullName.Replace($projectPath, "").TrimStart("\")
    $html += "    <li>$(HtmlEscape $relativePath) <span>($($file.Extension))</span></li>`n"
    $i++
}

$html += @"
  </ol>
</div>
"@

# Add file sections
$fileNum = 1
foreach ($file in $files) {
    $relativePath = $file.FullName.Replace($projectPath, "").TrimStart("\")
    $fileName = $file.Name
    $ext = $file.Extension.TrimStart(".")

    Write-Host "  Adding [$fileNum/$($files.Count)]: $relativePath" -ForegroundColor Gray

    try {
        $rawContent = Get-Content -Path $file.FullName -Raw -Encoding UTF8 -ErrorAction Stop
        if ($null -eq $rawContent) { $rawContent = "(empty file)" }
    } catch {
        $rawContent = "(could not read file: $($_.Exception.Message))"
    }

    $escapedContent = HtmlEscape $rawContent

    # Wrap each line in a span for line numbers
    $lines = $escapedContent -split "`n"
    $numberedLines = ($lines | ForEach-Object { "<span class='line'>$_</span>" }) -join "`n"

    $html += @"

<!-- FILE: $relativePath -->
<div class="file-section">
  <div class="file-header">
    <div>
      <div class="file-name">$fileName</div>
      <div class="file-path">$relativePath</div>
    </div>
    <div class="file-ext">$ext</div>
  </div>
  <pre>$numberedLines</pre>
</div>
"@

    $fileNum++
}

$html += @"

</body>
</html>
"@

# Write output
$html | Out-File -FilePath $outputFile -Encoding UTF8

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host " Export complete!" -ForegroundColor Green
Write-Host " File: $outputFile" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Cyan
Write-Host "  1. Open the file in Chrome or Edge"
Write-Host "  2. Press Ctrl+P (Print)"
Write-Host "  3. Set: Destination = 'Save as PDF'"
Write-Host "  4. Layout = Portrait or Landscape"
Write-Host "  5. Enable 'Background graphics'"
Write-Host "  6. Scale = Default (100%) or 80% if too wide"
Write-Host "  7. Click Save"
Write-Host ""

# Auto-open in default browser
Start-Process $outputFile
