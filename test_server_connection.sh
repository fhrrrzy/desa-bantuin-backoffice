#!/bin/bash

# Simple server connection test
BASE_URL="http://127.0.0.1:8001"

echo "Testing server connection..."
echo "Base URL: $BASE_URL"
echo ""

# Test if server is running
echo "1. Testing server availability..."
if curl -s --head "$BASE_URL" | head -n 1 | grep "HTTP/1.[01] [23].." > /dev/null; then
    echo "✅ Server is running and accessible"
else
    echo "❌ Server is not accessible at $BASE_URL"
    echo "Make sure your Laravel application is running with:"
    echo "php artisan serve --host=127.0.0.1 --port=8001"
    exit 1
fi

echo ""

# Test API endpoint
echo "2. Testing API endpoint..."
API_RESPONSE=$(curl -s "$BASE_URL/api/laporan-types" 2>/dev/null)

if [ $? -eq 0 ] && [ ! -z "$API_RESPONSE" ]; then
    echo "✅ API endpoint is accessible"
    echo "Response preview:"
    echo "$API_RESPONSE" | head -c 200
    echo "..."
else
    echo "❌ API endpoint is not accessible"
    echo "Make sure your routes are properly configured"
fi

echo ""
echo "Server connection test completed!" 