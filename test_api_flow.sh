#!/bin/bash

# Comprehensive API Test Script for Desa Bantuin Backoffice
# This script tests the complete user flow from registration to logout

BASE_URL="http://127.0.0.1:8001/api"
TOKEN=""
USER_ID=""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Function to make HTTP requests and handle responses
make_request() {
    local method=$1
    local endpoint=$2
    local data=$3
    local headers=$4
    
    print_status "Making $method request to $endpoint"
    
    if [ "$method" = "GET" ]; then
        response=$(curl -s -w "\n%{http_code}" "$BASE_URL$endpoint" $headers)
    elif [ "$method" = "POST" ]; then
        response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL$endpoint" $data $headers)
    elif [ "$method" = "PUT" ]; then
        response=$(curl -s -w "\n%{http_code}" -X PUT "$BASE_URL$endpoint" $data $headers)
    elif [ "$method" = "DELETE" ]; then
        response=$(curl -s -w "\n%{http_code}" -X DELETE "$BASE_URL$endpoint" $headers)
    fi
    
    # Extract status code (last line)
    status_code=$(echo "$response" | tail -n1)
    # Extract response body (all lines except last)
    response_body=$(echo "$response" | head -n -1)
    
    echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"
    
    if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
        print_success "Request successful (Status: $status_code)"
        return 0
    else
        print_error "Request failed (Status: $status_code)"
        return 1
    fi
}

# Function to extract token from response
extract_token() {
    echo "$1" | jq -r '.data.token' 2>/dev/null
}

# Function to extract user ID from response
extract_user_id() {
    echo "$1" | jq -r '.data.user.id' 2>/dev/null
}

# Function to extract laporan type ID from response
extract_laporan_type_id() {
    echo "$1" | jq -r '.data[0].id' 2>/dev/null
}

# Function to extract information ID from response
extract_information_id() {
    echo "$1" | jq -r '.data[0].id' 2>/dev/null
}

# Function to extract request ID from response
extract_request_id() {
    echo "$1" | jq -r '.data.request.id' 2>/dev/null
}

echo "=========================================="
echo "   Desa Bantuin API Comprehensive Test"
echo "=========================================="
echo ""

# Test 1: User Registration
print_status "1. Testing User Registration"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/register" \
    -H "Content-Type: application/json" \
    -d '{
        "name": "Test User",
        "email": "testuser@example.com",
        "phone_number": "081234567895",
        "password": "password123",
        "password_confirmation": "password123"
    }')

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Registration successful (Status: $status_code)"
    TOKEN=$(extract_token "$response_body")
    USER_ID=$(extract_user_id "$response_body")
    print_status "Token: $TOKEN"
    print_status "User ID: $USER_ID"
else
    print_error "Registration failed (Status: $status_code)"
    print_warning "Trying login with existing user instead..."
    
    # Try login with existing seeded user
    response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/login" \
        -H "Content-Type: application/json" \
        -d '{
            "phone_number": "081234567891",
            "password": "warga123"
        }')
    
    status_code=$(echo "$response" | tail -n1)
    response_body=$(echo "$response" | head -n -1)
    
    echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"
    
    if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
        print_success "Login successful (Status: $status_code)"
        TOKEN=$(extract_token "$response_body")
        USER_ID=$(extract_user_id "$response_body")
        print_status "Token: $TOKEN"
        print_status "User ID: $USER_ID"
    else
        print_error "Login failed (Status: $status_code)"
        exit 1
    fi
fi

echo ""

# Test 2: Get Laporan Types
print_status "2. Testing Get Laporan Types"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/laporan-types")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get Laporan Types successful (Status: $status_code)"
    LAPORAN_TYPE_ID=$(extract_laporan_type_id "$response_body")
    print_status "First Laporan Type ID: $LAPORAN_TYPE_ID"
else
    print_error "Get Laporan Types failed (Status: $status_code)"
    exit 1
fi

echo ""

# Test 3: Get Information by Laporan Type
print_status "3. Testing Get Information by Laporan Type"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/information/laporan-type/$LAPORAN_TYPE_ID")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get Information by Laporan Type successful (Status: $status_code)"
    INFORMATION_ID=$(extract_information_id "$response_body")
    print_status "First Information ID: $INFORMATION_ID"
else
    print_warning "Get Information by Laporan Type failed (Status: $status_code)"
    print_status "This might be normal if no information exists for this laporan type"
fi

echo ""

# Test 4: Get All Information
print_status "4. Testing Get All Information"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/information")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get All Information successful (Status: $status_code)"
    if [ -z "$INFORMATION_ID" ]; then
        INFORMATION_ID=$(extract_information_id "$response_body")
        print_status "First Information ID: $INFORMATION_ID"
    fi
else
    print_error "Get All Information failed (Status: $status_code)"
fi

echo ""

# Test 5: Create User Request
print_status "5. Testing Create User Request"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/requests" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: multipart/form-data" \
    -F "laporan_type_id=$LAPORAN_TYPE_ID" \
    -F "type=permintaan" \
    -F "description=Test request for API testing purposes")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Create User Request successful (Status: $status_code)"
    REQUEST_ID=$(extract_request_id "$response_body")
    print_status "Created Request ID: $REQUEST_ID"
else
    print_error "Create User Request failed (Status: $status_code)"
    exit 1
fi

echo ""

# Test 6: Get User's Requests List
print_status "6. Testing Get User's Requests List"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/requests" \
    -H "Authorization: Bearer $TOKEN")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get User's Requests List successful (Status: $status_code)"
else
    print_error "Get User's Requests List failed (Status: $status_code)"
fi

echo ""

# Test 7: Get Specific User Request
print_status "7. Testing Get Specific User Request"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/requests/$REQUEST_ID" \
    -H "Authorization: Bearer $TOKEN")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get Specific User Request successful (Status: $status_code)"
else
    print_error "Get Specific User Request failed (Status: $status_code)"
fi

echo ""

# Test 8: Get User Profile
print_status "8. Testing Get User Profile"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/profile" \
    -H "Authorization: Bearer $TOKEN")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get User Profile successful (Status: $status_code)"
else
    print_error "Get User Profile failed (Status: $status_code)"
fi

echo ""

# Test 9: Get Request Statistics
print_status "9. Testing Get Request Statistics"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" "$BASE_URL/requests/statistics" \
    -H "Authorization: Bearer $TOKEN")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Get Request Statistics successful (Status: $status_code)"
else
    print_error "Get Request Statistics failed (Status: $status_code)"
fi

echo ""

# Test 10: Logout
print_status "10. Testing Logout"
echo "----------------------------------------"
response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/logout" \
    -H "Authorization: Bearer $TOKEN")

status_code=$(echo "$response" | tail -n1)
response_body=$(echo "$response" | head -n -1)

echo "$response_body" | jq '.' 2>/dev/null || echo "$response_body"

if [ "$status_code" -ge 200 ] && [ "$status_code" -lt 300 ]; then
    print_success "Logout successful (Status: $status_code)"
else
    print_error "Logout failed (Status: $status_code)"
fi

echo ""
echo "=========================================="
echo "   Test Summary"
echo "=========================================="
print_success "User ID: $USER_ID"
print_success "Laporan Type ID: $LAPORAN_TYPE_ID"
print_success "Information ID: $INFORMATION_ID"
print_success "Request ID: $REQUEST_ID"
print_success "Token: $TOKEN"
echo ""
print_success "All tests completed! Check the output above for any errors."
echo "" 