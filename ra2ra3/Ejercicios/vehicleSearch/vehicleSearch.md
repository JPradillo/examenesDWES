# 🚗 Vehicle Search Form – DWES Recovery Exam

This PHP project implements a vehicle search form with file upload, validation, and data matching from a CSV file. It was developed as part of a recovery exam for the DWES (Server-side Web Development) course.

---

## 🧰 Features

- Collects vehicle search parameters from the user.
- Validates form data including email, vehicle type, brand, and age.
- Uploads and verifies a CSV file containing vehicle records.
- Displays search results based on matching form input with CSV entries.

---

## 📝 Form Fields

The form includes:

- **Email**: Must be a valid email address.
- **Vehicle Type**: Radio button – `Tourism` or `Van`.
- **Brand**: Dropdown – chooses from `Fiat`, `Opel`, or `Mercedes`.
- **Age (Years)**: Integer between 1 and 5.
- **ITV Passed**: Checkbox (default checked).
- **CSV File Upload**: Only accepts files of MIME type `text/csv`. Maximum size is 200KB.

---

## 🛡️ Input Validation & Sanitization

The form uses PHP's filter mechanisms to:

- **Sanitize**:
  - `email`: Sanitized using `FILTER_SANITIZE_EMAIL`.
  - `type`, `brand`: Sanitized with `FILTER_SANITIZE_SPECIAL_CHARS`.
  - `age`: Sanitized as an integer.
- **Validate**:
  - `email`: Must be valid.
  - `type` and `brand`: Checked against predefined keys.
  - `age`: Must be an integer from 1 to 5.
  - `ITV`: Validated as boolean.

---

## 📂 File Upload Handling

- Only accepts CSV files.
- Checks MIME type using:
  - `$_FILES['type']`
  - `mime_content_type()`
  - `finfo_file()` with `finfo_open()`
- Rejects upload if:
  - File exceeds 200 KB.
  - File type is not CSV.
  - Upload fails.

---

## 🔍 CSV File Search

- Reads the uploaded CSV file line by line.
- Matches each row with the following:
  - Vehicle type
  - Brand
  - Age
  - ITV status ("Yes"/"No")
- Displays matching rows in a table format.

---

## 🗃 Folder Structure

```
recuperacion/
    ├── includes/
    |       └── funciones.php
    ├── estilos/
    |       ├── general.css
    |       ├── tablas.css 
    |       └── formulario.css
    └── ra2ra3/
            └── Ejercicios
                    └── vehicleSearch
                            └── vehicleSearch.php
```

---

## ▶️ How to Use

1. Place the project folder (`recuperacion/`) into your local server’s root (`htdocs` or `www`).
2. Launch your local server (XAMPP, WAMP, etc.).
3. Open the browser and access the script:  
   `http://localhost/recuperacion/[filename].php`
4. Fill out the form and upload a valid `.csv` file with vehicle data.
5. Submit and view matched results.

---

## ⚠️ Error Handling

- Provides meaningful messages for:
  - Invalid or missing inputs
  - File type errors
  - Oversized uploads
  - Unvalidated data

---

## ✍️ Author

Practical project for the DWES course.  
Created by: JPradillo7