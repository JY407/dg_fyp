# Test Registration Flow
Write-Host "Testing Registration..." -ForegroundColor Cyan

# First, get the registration page to obtain CSRF token
$registerPage = Invoke-WebRequest -Uri "http://127.0.0.1:8000/register" -SessionVariable session -UseBasicParsing
Write-Host "✓ Registration page loaded (Status: $($registerPage.StatusCode))" -ForegroundColor Green

# Extract CSRF token from the page
if ($registerPage.Content -match 'name="_token"\s+value="([^"]+)"') {
    $csrfToken = $matches[1]
    Write-Host "✓ CSRF token found: $($csrfToken.Substring(0,20))..." -ForegroundColor Green
} else {
    Write-Host "✗ CSRF token not found in page" -ForegroundColor Red
    exit 1
}

# Prepare registration data
$timestamp = Get-Date -Format "HHmmss"
$testEmail = "demo$timestamp@test.com"
$body = @{
    _token = $csrfToken
    name = "Demo Test User"
    email = $testEmail
    unit_number = "A-12-05"
    user_type = "owner"
    block = "Block A"
    street = "Main Street"
    password = "DemoPassword123!"
    password_confirmation = "DemoPassword123!"
}

Write-Host "`nAttempting to register user:" -ForegroundColor Cyan
Write-Host "  Email: $testEmail"
Write-Host "  Name: Demo Test User"
Write-Host "  Unit: A-12-05"

# Submit registration form
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/register" `
        -Method POST `
        -Body $body `
        -WebSession $session `
        -MaximumRedirection 0 `
        -ErrorAction SilentlyContinue
    
    Write-Host "`n✓ Registration request sent" -ForegroundColor Green
    Write-Host "  Status Code: $($response.StatusCode)"
    Write-Host "  Status: $($response.StatusDescription)"
} catch {
    if ($_.Exception.Response.StatusCode -eq 302) {
        Write-Host "`n✓ Registration successful! (Redirected)" -ForegroundColor Green
        $redirectUrl = $_.Exception.Response.Headers.Location
        Write-Host "  Redirect to: $redirectUrl"
    } else {
        Write-Host "`n✗ Registration failed" -ForegroundColor Red
        Write-Host "  Error: $($_.Exception.Message)"
    }
}

Write-Host "`n" -NoNewline
