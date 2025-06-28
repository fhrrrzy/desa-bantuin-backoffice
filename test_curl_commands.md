# Individual API Test Commands

This file contains individual curl commands for testing each API endpoint separately.

## Base URL
```bash
BASE_URL="http://127.0.0.1:8001/api"
```

## 1. User Registration
```bash
curl -X POST "$BASE_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "testuser@example.com",
    "phone_number": "081234567890",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

## 2. User Login
```bash
curl -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "081234567890",
    "password": "password123"
  }'
```

## 3. Get Laporan Types
```bash
curl -X GET "$BASE_URL/laporan-types" \
  -H "Content-Type: application/json"
```

## 4. Get All Information
```bash
curl -X GET "$BASE_URL/information" \
  -H "Content-Type: application/json"
```

## 5. Get Information by Laporan Type
```bash
# Replace {laporan_type_id} with actual ID from step 3
curl -X GET "$BASE_URL/information/laporan-type/1" \
  -H "Content-Type: application/json"
```

## 6. Create User Request
```bash
# Replace {token} with token from step 2
# Replace {laporan_type_id} with actual ID from step 3
curl -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: multipart/form-data" \
  -F "laporan_type_id=1" \
  -F "type=permintaan" \
  -F "description=Test request for API testing purposes"
```

## 7. Get User's Requests List
```bash
# Replace {token} with token from step 2
curl -X GET "$BASE_URL/requests" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

## 8. Get Specific User Request
```bash
# Replace {token} with token from step 2
# Replace {request_id} with actual ID from step 6
curl -X GET "$BASE_URL/requests/1" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

## 9. Get User Profile
```bash
# Replace {token} with token from step 2
curl -X GET "$BASE_URL/profile" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

## 10. Get Request Statistics
```bash
# Replace {token} with token from step 2
curl -X GET "$BASE_URL/requests/statistics" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

## 11. Logout
```bash
# Replace {token} with token from step 2
curl -X POST "$BASE_URL/logout" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

## 12. Create Information (Admin)
```bash
curl -X POST "$BASE_URL/information" \
  -H "Content-Type: multipart/form-data" \
  -F "title=Test Information" \
  -F "description=This is a test information entry" \
  -F "laporan_type_id=1"
```

## 13. Update Information (Admin)
```bash
# Replace {information_id} with actual ID
curl -X PUT "$BASE_URL/information/1" \
  -H "Content-Type: multipart/form-data" \
  -F "title=Updated Test Information" \
  -F "description=This is an updated test information entry" \
  -F "laporan_type_id=1"
```

## 14. Delete Information (Admin)
```bash
# Replace {information_id} with actual ID
curl -X DELETE "$BASE_URL/information/1" \
  -H "Content-Type: application/json"
```

## 15. Get Specific Information
```bash
# Replace {information_id} with actual ID
curl -X GET "$BASE_URL/information/1" \
  -H "Content-Type: application/json"
```

## Testing with File Uploads

### Create Information with File Upload
```bash
curl -X POST "$BASE_URL/information" \
  -H "Content-Type: multipart/form-data" \
  -F "title=Information with Files" \
  -F "description=This information has attached files" \
  -F "laporan_type_id=1" \
  -F "attachment[]=@/path/to/file1.pdf" \
  -F "attachment[]=@/path/to/file2.jpg"
```

### Create User Request with File Upload
```bash
# Replace {token} with token from step 2
curl -X POST "$BASE_URL/requests" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: multipart/form-data" \
  -F "laporan_type_id=1" \
  -F "type=permintaan" \
  -F "description=Request with attachments" \
  -F "lampiran[]=@/path/to/file1.pdf" \
  -F "lampiran[]=@/path/to/file2.jpg"
```

## Quick Test Script

You can also run the comprehensive test script:

```bash
./test_api_flow.sh
```

## Notes

- Make sure your Laravel application is running on `http://127.0.0.1:8001`
- Replace placeholder values like `{token}`, `{laporan_type_id}`, etc. with actual values
- The script will automatically handle token extraction and reuse
- All responses are formatted with `jq` if available, otherwise raw JSON is displayed
- File uploads require actual files to exist at the specified paths 