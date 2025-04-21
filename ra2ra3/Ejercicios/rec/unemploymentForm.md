# 📝 Job Applicant Form – DWES Recovery Exam

This project is a PHP-based web application that simulates a **job applicant request form**, created as part of a **recovery exam** for the course **Server-Side Web Development (DWES)**.

---

## 📌 Features

The form allows users to:

- Enter an **email address**.
- Select one or more **available courses** (with their price).
- Specify the number of **in-person classes** (between 5 and 10).
- Mark whether they are currently **unemployed**.
- **Upload a PDF file** representing a job seeker card.

---

## 🧠 Validation & Logic

- Email is **sanitized and validated**.
- Selected courses are checked against a fixed list to avoid tampering.
- Number of classes must be between 5 and 10.
- If the unemployment box is checked, a valid **PDF file** must be uploaded:
  - MIME type is validated using `mime_content_type`, `$_FILES['type']`, and `finfo_file`.
- File is saved into a specific directory under `recuperacion/ra2ra3/Ejercicios/examen24/tarjetas/`.

---

## 📂 Project Structure

```markdown
recuperacion/
    ├── includes/
    |       └── funciones.php
    └── estilos/
            ├── general.css
            ├── tablas.css 
            └── formulario.css
    ├── exercise.php
    └── ra2ra3/
            └── Ejercicios/
                    ├── examen24/
                    └── tarjetas/
```


---

## 💰 Budget Calculation

- Adds the total price of the selected courses.
- Adds the cost of the classes (10€/class).
- If the user is unemployed, a **10% discount** is applied.

---

## 🖥️ Technologies

- Language: PHP 8.x
- HTML5 + CSS3
- No JavaScript (fully backend)
- File upload handling and server-side validation
- Secure filtering and error control

---

## 🚀 How to Run

1. Make sure you have a local server (XAMPP, Laragon, MAMP, etc.).
2. Place the `recuperacion/` folder inside your server's `htdocs` or root directory.
3. Open your browser and go to:  
   `http://localhost/recuperacion/exercise1.php`

---

## 📧 Author

Practical project for the DWES course.  
Created by: JPradillo7