<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}">
    </script>
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div id="app" class="container p-4 bg-white shadow rounded" style="max-width: 400px;">
        <h2 class="text-center text-primary mb-3">Booking Penyewaan PS</h2>

        <!-- Input Nama -->
        <div class="mb-3">
            <label class="form-label fw-bold">Nama:</label>
            <input type="text" v-model="form.name" class="form-control" placeholder="Masukkan Nama">
            <small v-if="errors.name" class="text-danger">@{{ errors . name }}</small>
        </div>

        <!-- Input Email -->
        <div class="mb-3">
            <label class="form-label fw-bold">Email:</label>
            <input type="email" v-model="form.email" class="form-control" placeholder="Masukkan Email">
            <small v-if="errors.email" class="text-danger">@{{ errors . email }}</small>
        </div>

        <!-- Input Nomor Telepon -->
        <div class="mb-3">
            <label class="form-label fw-bold">Nomor Telepon:</label>
            <input type="text" v-model="form.phone" class="form-control" placeholder="Masukkan Nomor Telepon">
            <small v-if="errors.phone" class="text-danger">@{{ errors . phone }}</small>
        </div>

        <!-- Input Tanggal -->
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih Tanggal:</label>
            <input type="date" v-model="form.booking_date" @change="calculatePrice" class="form-control">
            <small v-if="errors.booking_date" class="text-danger">@{{ errors . booking_date }}</small>
        </div>

        <!-- Pilihan Konsol -->
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih Konsol:</label>
            <select v-model="form.console_type" @change="calculatePrice" class="form-select">
                <option value="PS4">PS4 - Rp30.000 / sesi</option>
                <option value="PS5">PS5 - Rp40.000 / sesi</option>
            </select>
        </div>

        <!-- Total Harga -->
        <div class="mb-3">
            <p class="fw-bold fs-5 text-success">
                Total Harga: <span class="text-primary">Rp@{{ totalPrice }}</span>
            </p>
        </div>

        <!-- Tombol Pesan -->
        <button @click="submitBooking" class="btn btn-primary w-100">Pesan Sekarang</button>
        <a href="{{ route('orders.lists') }}" class="btn btn-secondary w-100 mt-2">Lihat Pesanan</a>

        <!-- Tombol Bayar -->
        <div v-if="snapToken" class="mt-3 text-center">
            <button @click="payNow" class="btn btn-success">Bayar Sekarang</button>
        </div>

        <!-- Pesan Sukses -->
        <p v-if="message" class="alert alert-success text-center mt-3">@{{ message }}</p>
    </div>

    <script>
        const {
            createApp
        } = Vue;

        createApp({
            data() {
                return {
                    form: {
                        name: '',
                        email: '',
                        phone: '',
                        booking_date: '',
                        console_type: 'PS4',
                        total_price: 0,
                    },
                    snapToken: null,
                    message: '',
                    errors: {}
                };
            },
            computed: {
                totalPrice() {
                    return this.form.total_price.toLocaleString("id-ID");
                }
            },
            methods: {
                // Hitung harga otomatis
                calculatePrice() {
                    if (!this.form.booking_date || !this.form.console_type) return;

                    let price = this.form.console_type === 'PS4' ? 30000 : 40000;
                    let date = new Date(this.form.booking_date);
                    let day = date.getDay(); // 0 (Minggu) - 6 (Sabtu)

                    if (day === 0 || day === 6) {
                        price += 50000; // Tambahan biaya Sabtu/Minggu
                    }

                    this.form.total_price = price;
                },

                // Validasi Form
                validateForm() {
                    this.errors = {};

                    if (!this.form.name) this.errors.name = "Nama wajib diisi!";
                    if (!this.form.email) {
                        this.errors.email = "Email wajib diisi!";
                    } else if (!/\S+@\S+\.\S+/.test(this.form.email)) {
                        this.errors.email = "Format email tidak valid!";
                    }
                    if (!this.form.phone) {
                        this.errors.phone = "Nomor telepon wajib diisi!";
                    } else if (!/^\d+$/.test(this.form.phone)) {
                        this.errors.phone = "Nomor telepon hanya boleh angka!";
                    }
                    if (!this.form.booking_date) this.errors.booking_date = "Tanggal wajib dipilih!";

                    return Object.keys(this.errors).length === 0;
                },

                // Kirim Data Booking ke Laravel
                submitBooking() {
                    if (!this.validateForm()) return;

                    axios.post('/orders', this.form)
                        .then(response => {
                            this.message = "Pesanan berhasil dibuat!";
                            this.snapToken = response.data.snap_token;
                        })
                        .catch(error => {
                            console.error(error);
                        });
                },

                // Jalankan Pembayaran Midtrans
                payNow() {
                    window.snap.pay(this.snapToken, {
                        onSuccess: (result) => {
                            alert("Pembayaran Berhasil!");
                            console.log(result);
                        },
                        onPending: (result) => {
                            alert("Menunggu Pembayaran...");
                            console.log(result);
                        },
                        onError: (result) => {
                            alert("Pembayaran Gagal!");
                            console.log(result);
                        },
                    });
                }
            }
        }).mount('#app');
    </script>

</body>

</html>
