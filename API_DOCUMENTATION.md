# API Documentation - Desa Bantuin

## Base URL

```
http://localhost:8000/api
```

## Authentication Endpoints

### 1. Register User

**POST** `/api/register`

Register a new user with phone number authentication.

**Request Body:**

```json
{
    "name": "Ahmad Supriadi",
    "email": "ahmad@example.com",
    "phone_number": "081234567892",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Registrasi berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Ahmad Supriadi",
            "email": "ahmad@example.com",
            "phone_number": "081234567892",
            "role": "warga"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### 2. Login with Phone Number

**POST** `/api/login`

Login using phone number and password.

**Request Body:**

```json
{
    "phone_number": "081234567891",
    "password": "warga123"
}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 2,
            "name": "Warga Test",
            "email": "warga@desa-bantuin.com",
            "phone_number": "081234567891",
            "role": "warga"
        },
        "token": "2|def456...",
        "token_type": "Bearer"
    }
}
```

### 3. Get User Profile

**GET** `/api/profile`

Get authenticated user's profile information.

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Data profil berhasil diambil",
    "data": {
        "user": {
            "id": 2,
            "name": "Warga Test",
            "email": "warga@desa-bantuin.com",
            "phone_number": "081234567891",
            "role": "warga",
            "avatar_url": null
        }
    }
}
```

### 4. Update User Profile

**PUT** `/api/profile`

Update authenticated user's profile information.

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "name": "Warga Test Updated",
    "email": "warga.updated@desa-bantuin.com",
    "phone_number": "081234567895"
}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Profil berhasil diperbarui",
    "data": {
        "user": {
            "id": 2,
            "name": "Warga Test Updated",
            "email": "warga.updated@desa-bantuin.com",
            "phone_number": "081234567895",
            "role": "warga",
            "avatar_url": null
        }
    }
}
```

### 5. Change Password

**POST** `/api/change-password`

Change user's password.

**Headers:**

```
Authorization: Bearer {token}
```

**Request Body:**

```json
{
    "current_password": "password123",
    "new_password": "newpassword123",
    "new_password_confirmation": "newpassword123"
}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Kata sandi berhasil diubah. Silakan login kembali."
}
```

### 6. Refresh Token

**POST** `/api/refresh`

Refresh the current access token.

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Token berhasil diperbarui",
    "data": {
        "user": {
            "id": 2,
            "name": "Warga Test",
            "email": "warga@desa-bantuin.com",
            "phone_number": "081234567891",
            "role": "warga"
        },
        "token": "3|ghi789...",
        "token_type": "Bearer"
    }
}
```

### 7. Logout

**POST** `/api/logout`

Logout user and revoke all tokens.

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

## Laporan Type Endpoints

### 8. Get All Laporan Types

**GET** `/api/laporan-types`

Get all available laporan types (public endpoint).

**Response (200):**

```json
{
    "success": true,
    "message": "Laporan types retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "KTP",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        },
        {
            "id": 2,
            "name": "KK",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ]
}
```

## User Request Endpoints

### 9. Get User's Requests

**GET** `/api/requests`

Get authenticated user's requests with pagination and filtering.

**Headers:**

```
Authorization: Bearer {token}
```

**Query Parameters:**

-   `per_page` (optional): Number of items per page (default: 10)
-   `page` (optional): The page number to retrieve (default: 1)
-   `status` (optional): Filter by status (onprocess, accepted, rejected)
-   `type` (optional): Filter by type (permintaan, pelaporan)

**Response (200):**

```json
{
    "success": true,
    "message": "Data permintaan berhasil diambil",
    "data": {
        "requests": [
            {
                "id": 1,
                "laporan_type": {
                    "id": 1,
                    "name": "KTP"
                },
                "title": "Permintaan KTP Baru",
                "type": "permintaan",
                "description": "Saya ingin mengajukan pembuatan KTP baru",
                "status": "onprocess",
                "return_message": null,
                "lampiran": ["lampiran/file1.pdf"],
                "created_at": "2024-01-15T10:30:00.000000Z",
                "updated_at": "2024-01-15T10:30:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 1,
            "per_page": 10,
            "total": 1,
            "from": 1,
            "to": 1
        }
    }
}
```

### 10. Get Specific Request

**GET** `/api/requests/{id}`

Get a specific request by ID (only if it belongs to the authenticated user).

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Data permintaan berhasil diambil",
    "data": {
        "request": {
            "id": 1,
            "laporan_type": {
                "id": 1,
                "name": "KTP"
            },
            "title": "Permintaan KTP Baru",
            "type": "permintaan",
            "description": "Saya ingin mengajukan pembuatan KTP baru",
            "status": "onprocess",
            "return_message": null,
            "lampiran": ["lampiran/file1.pdf"],
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    }
}
```

### 11. Create New Request

**POST** `/api/requests`

Create a new user request.

**Headers:**

```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**

```json
{
    "laporan_type_id": 1,
    "title": "Permintaan KTP Baru",
    "type": "permintaan",
    "description": "Saya ingin mengajukan pembuatan KTP baru",
    "lampiran": [file1, file2] // Optional files
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Permintaan berhasil dibuat",
    "data": {
        "request": {
            "id": 1,
            "laporan_type": {
                "id": 1,
                "name": "KTP"
            },
            "title": "Permintaan KTP Baru",
            "type": "permintaan",
            "description": "Saya ingin mengajukan pembuatan KTP baru",
            "status": "onprocess",
            "lampiran": ["lampiran/file1.pdf"],
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    }
}
```

### 12. Get Request Statistics

**GET** `/api/requests/statistics`

Get statistics for authenticated user's requests.

**Headers:**

```
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Statistik permintaan berhasil diambil",
    "data": {
        "statistics": {
            "total": 5,
            "onprocess": 2,
            "selesai": 2,
            "ditolak": 1,
            "permintaan": 3,
            "pelaporan": 2
        }
    }
}
```

## Information Endpoints

### 13. Get All Information

**GET** `/api/information`

Get all information with pagination and filtering.

**Query Parameters:**

-   `per_page` (optional): Number of items per page (default: 10)
-   `page` (optional): The page number to retrieve (default: 1)
-   `laporan_type_id` (optional): Filter by laporan type ID
-   `search` (optional): Search by title

**Response (200):**

```json
{
    "success": true,
    "message": "Information retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Cara Membuat KTP",
            "description": "Panduan lengkap untuk membuat KTP baru",
            "laporan_type": {
                "id": 1,
                "name": "KTP"
            },
            "attachment": [
                {
                    "filename": "template-ktp.pdf",
                    "url": "http://localhost:8000/storage/information-attachments/template-ktp.pdf",
                    "path": "information-attachments/template-ktp.pdf"
                }
            ],
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}
```

### 14. Get Specific Information

**GET** `/api/information/{id}`

Get a specific information by ID.

**Response (200):**

```json
{
    "success": true,
    "message": "Information retrieved successfully",
    "data": {
        "id": 1,
        "title": "Cara Membuat KTP",
        "description": "Panduan lengkap untuk membuat KTP baru",
        "laporan_type": {
            "id": 1,
            "name": "KTP"
        },
        "attachment": [
            {
                "filename": "template-ktp.pdf",
                "url": "http://localhost:8000/storage/information-attachments/template-ktp.pdf",
                "path": "information-attachments/template-ktp.pdf"
            }
        ],
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

### 15. Get Information by Laporan Type

**GET** `/api/information/laporan-type/{laporanTypeId}`

Get all information for a specific laporan type.

**Response (200):**

```json
{
    "success": true,
    "message": "Information retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Cara Membuat KTP",
            "description": "Panduan lengkap untuk membuat KTP baru",
            "laporan_type": {
                "id": 1,
                "name": "KTP"
            },
            "attachment": [
                {
                    "filename": "template-ktp.pdf",
                    "url": "http://localhost:8000/storage/information-attachments/template-ktp.pdf",
                    "path": "information-attachments/template-ktp.pdf"
                }
            ],
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ]
}
```

## Error Responses

### Validation Error (422)

```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "phone_number": ["Nomor telepon wajib diisi"]
    }
}
```

### Authentication Error (401)

```json
{
    "success": false,
    "message": "Nomor telepon atau kata sandi salah"
}
```

### Authorization Error (403)

```json
{
    "success": false,
    "message": "Anda tidak memiliki akses ke permintaan ini"
}
```

### Not Found Error (404)

```json
{
    "success": false,
    "message": "Permintaan tidak ditemukan"
}
```

### Server Error (500)

```json
{
    "success": false,
    "message": "Terjadi kesalahan saat login",
    "error": "Error details..."
}
```

## Usage Examples

### Using cURL

**Register:**

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Ahmad Supriadi",
    "email": "ahmad@example.com",
    "phone_number": "081234567892",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login:**

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "phone_number": "081234567891",
    "password": "warga123"
  }'
```

**Get User's Requests:**

```bash
curl -X GET "http://localhost:8000/api/requests?status=onprocess&per_page=5&page=2" \
  -H "Authorization: Bearer {your_token_here}"
```

**Create New Request:**

```bash
curl -X POST http://localhost:8000/api/requests \
  -H "Authorization: Bearer {your_token_here}" \
  -F "laporan_type_id=1" \
  -F "type=permintaan" \
  -F "description=Saya ingin mengajukan pembuatan KTP baru" \
  -F "lampiran[]=@/path/to/file1.pdf" \
  -F "lampiran[]=@/path/to/file2.jpg"
```

**Get Profile (with token):**

```bash
curl -X GET http://localhost:8000/api/profile \
  -H "Authorization: Bearer {your_token_here}"
```

**Refresh Token:**

```bash
curl -X POST http://localhost:8000/api/refresh \
  -H "Authorization: Bearer {your_token_here}"
```

**Get All Information:**

```bash
curl -X GET "http://localhost:8000/api/information?laporan_type_id=1&search=KTP" \
  -H "Content-Type: application/json"
```

**Get Specific Information:**

```bash
curl -X GET http://localhost:8000/api/information/1 \
  -H "Content-Type: application/json"
```

**Get Information by Laporan Type:**

```bash
curl -X GET http://localhost:8000/api/information/laporan-type/1 \
  -H "Content-Type: application/json"
```

### Using JavaScript/Fetch

**Login:**

```javascript
const response = await fetch("http://localhost:8000/api/login", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
    },
    body: JSON.stringify({
        phone_number: "081234567891",
        password: "warga123",
    }),
});

const data = await response.json();
const token = data.data.token;

// Store token for future requests
localStorage.setItem("auth_token", token);
```

**Get User's Requests:**

```javascript
const token = localStorage.getItem("auth_token");

const response = await fetch(
    "http://localhost:8000/api/requests?status=onprocess&page=2",
    {
        method: "GET",
        headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    }
);

const data = await response.json();
console.log(data.data.requests);
```

**Create New Request:**

```javascript
const token = localStorage.getItem("auth_token");
const formData = new FormData();

formData.append("laporan_type_id", "1");
formData.append("type", "permintaan");
formData.append("description", "Saya ingin mengajukan pembuatan KTP baru");

// Add files if any
const fileInput = document.getElementById("fileInput");
for (let file of fileInput.files) {
    formData.append("lampiran[]", file);
}

const response = await fetch("http://localhost:8000/api/requests", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${token}`,
    },
    body: formData,
});

const data = await response.json();
```

**Refresh Token:**

```javascript
const token = localStorage.getItem("auth_token");

const response = await fetch("http://localhost:8000/api/refresh", {
    method: "POST",
    headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json",
    },
});

const data = await response.json();

if (data.success) {
    // Update stored token with new one
    localStorage.setItem("auth_token", data.data.token);
    console.log("Token refreshed successfully");
}
```

**Get All Information:**

```javascript
const response = await fetch(
    "http://localhost:8000/api/information?laporan_type_id=1&search=KTP",
    {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
    }
);

const data = await response.json();
console.log(data.data);
```

**Get Specific Information:**

```javascript
const response = await fetch("http://localhost:8000/api/information/1", {
    method: "GET",
    headers: {
        "Content-Type": "application/json",
    },
});

const data = await response.json();
console.log(data.data);
```

**Get Information by Laporan Type:**

```javascript
const response = await fetch("http://localhost:8000/api/information/laporan-type/1", {
    method: "GET",
    headers: {
        "Content-Type": "application/json",
    },
});

const data = await response.json();
console.log(data.data);
```

## Notes

-   All responses follow a consistent format with `success`, `message`, and `data` fields
-   Phone number authentication is the primary login method
-   Tokens are automatically revoked on logout and password change
-   All protected routes require the `Authorization: Bearer {token}` header
-   Users are automatically assigned the "warga" role upon registration
-   Users can only access their own request data
-   File uploads are supported for request attachments (max 5MB per file)
-   Information endpoints support multiple file uploads (max 10MB per file, up to 5 files)
-   Supported file types for information attachments: PDF, DOC, DOCX, JPG, JPEG, PNG
-   Pagination is available for request and information listing
-   Filtering by status and type is supported for requests
-   Filtering by laporan type and search by title is supported for information
-   Information endpoints are publicly accessible (no authentication required for read operations)
-   This API is designed specifically for warga (citizens) - admin functionality is handled through the web interface

## Seeded Test Users

The following users are available for testing after running the database seeders:

### Warga Users (Mobile App Users)
- **Warga Test**: `081234567891` / `warga123`
- **Ahmad Supriadi**: `081234567892` / `password123`
- **Siti Nurhaliza**: `081234567893` / `password123`
- **Budi Santoso**: `081234567894` / `password123`

**Note**: This API is specifically designed for mobile users with "warga" role. Admin functionality is handled through the web interface.
