# Download Chart.js (v4.4.0) to public/assets/js for offline use
$uri = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'
$dest = Join-Path $PSScriptRoot '..\public\assets\js\chart.umd.min.js' | Resolve-Path -Relative
$dest = [IO.Path]::GetFullPath($dest)
$dir = Split-Path $dest -Parent
if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Path $dir -Force | Out-Null }

Write-Host "Downloading $uri to $dest ..."
try {
    Invoke-WebRequest -Uri $uri -OutFile $dest -UseBasicParsing -ErrorAction Stop
    Write-Host "Downloaded to $dest"
} catch {
    Write-Error "Download failed: $_"
    exit 1
}
