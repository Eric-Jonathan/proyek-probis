<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Manual System</title>
    <style>
        /* resources/views/auth/style.css (NEW DESIGN: Orange Background, Navy Form) */
:root {
    --bg-page-orange: lavender;    /* Orange Cerah untuk Background */
    --card-navy-dark: #0a1929;    /* Navy Sangat Gelap untuk Card */
    --input-navy: #132f4c;        /* Navy Sedikit Terang untuk Input */
    --navy-button: #007bff;       /* Navy Cerah/Biru untuk Tombol */
    --navy-button-hover: #0056b3;
    --text-light: #f4f7f6;       /* Putih Gading untuk Teks */
    --text-muted: #94a3b8;        /* Abu-abu terang untuk deskripsi */
    --border-navy: #265d97;
    --error-red: #ff6b6b;
    --success-green: #4cd137;
}

body {
    font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: var(--bg-page-orange); /* Background Full Orange */
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

form {
    background-color: var(--card-navy-dark); /* Card warna Navy Gelap */
    width: 100%;
    max-width: 420px;
    padding: 50px 40px;
    border-radius: 16px; /* Sudut lebih tumpul agar modern */
    box-shadow: 0 20px 40px rgba(0,0,0,0.3); /* Shadow lebih dalam */
    box-sizing: border-box;
}

h2 {
    color: var(--text-light); /* Teks Judul Terang */
    text-align: center;
    margin-top: 0;
    margin-bottom: 35px;
    font-size: 30px;
    font-weight: 800;
}

label {
    color: var(--text-light); /* Label Terang */
    font-weight: 600;
    font-size: 0.9em;
    margin-bottom: 8px;
    display: block;
}

/* Penataan Input Field Navy */
input, select {
    width: 100%;
    padding: 14px 18px;
    margin-bottom: 25px;
    background-color: var(--input-navy); /* Input warna Navy */
    border: 2px solid var(--border-navy); /* Border Navy Cerah */
    border-radius: 10px;
    box-sizing: border-box;
    transition: all 0.2s ease;
    font-size: 1em;
    color: var(--text-light); /* Teks Input Terang */
}

input::placeholder {
    color: var(--text-muted); /* Placeholder tidak terlalu terang */
}

/* Fokus tetap bertema terang agar terlihat */
input:focus, select:focus {
    border-color: var(--text-light);
    outline: none;
    box-shadow: 0 0 0 3px rgba(244, 247, 246, 0.2);
}

select{
    padding-right: 2rem;
    appearance: none;          /* hapus default browser styling */
    -webkit-appearance: none;  /* Safari/Chrome */
    -moz-appearance: none;
    cursor: pointer;
}

/* Tombol Utama Navy Cerah */
button[type="submit"] {
    width: 100%;
    padding: 14px;
    background-color: var(--navy-button); /* Tombol Navy Cerah */
    color: var(--text-light);
    border: none;
    border-radius: 10px;
    font-size: 1.1em;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.2s ease;
    margin-top: 10px;
}

button[type="submit"]:hover {
    background-color: var(--navy-button-hover);
}

/* Teks Bawah */
p {
    text-align: center;
    margin-top: 25px;
    font-size: 0.9em;
    color: var(--text-muted);
}

a {
    color: var(--text-light); /* Link Putih agar terbaca */
    text-decoration: none;
    font-weight: 600;
}

a:hover {
    text-decoration: underline;
}

/* Error & Sukses (Diatur agar tetap readable di atas Navy) */
.error {
    color: var(--error-red);
    font-size: 0.85em;
    margin-top: -20px; /* Lebih tinggi untuk mendekati input */
    margin-bottom: 20px;
    display: block;
    font-weight: 500;
}

.success {
    background-color: #1e3a2f; /* Hijau Tua Gelap untuk background sukses */
    color: var(--success-green);
    padding: 14px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 25px;
    border: 1px solid var(--success-green);
    font-weight: 600;
}

/* Error Login di atas */
p.error {
    margin-top: 0;
    margin-bottom: 25px;
    padding: 14px;
    background-color: #3a1e1e; /* Merah Tua Gelap */
    border: 1px solid var(--error-red);
    border-radius: 10px;
}
    </style>
</head>
<body>
    <form action="{{ route('register.post') }}" method="POST">
        @csrf <h2>Register Account</h2>

        <label>Nama Lengkap</label>
        <input type="text" name="username" value="{{ old('username') }}" required>
        @error('username') <span class="error">{{ $message }}</span> @enderror

        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email') <span class="error">{{ $message }}</span> @enderror

        <label>Phone</label>
        <input type="text" name="phone" value="{{ old('phone') }}" required>
        @error('phone') <span class="error">{{ $message }}</span> @enderror

        <label>Password (Min 8 Karakter)</label>
        <input type="password" name="password" required>
        @error('password') <span class="error">{{ $message }}</span> @enderror

        <label>Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required>

        <label>Role</label>
        <select name="role" required>
            <option value="" selected disabled>Pilih Role</option>
            <option value="penyedia">Penyedia Tempat</option>
            <option value="penyewa">Penyewa</option>
        </select>
        @error('role') <span class="error">{{ $message }}</span> @enderror

        <button type="submit">Daftar Sekarang</button>
        <p>Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </form>
</body>
</html>