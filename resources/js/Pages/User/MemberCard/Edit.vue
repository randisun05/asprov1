<template>

    <Head>
        <title>Kartu Anggota</title>
    </Head>
    <div class="px-5 shadow padding">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <!-- Heading -->

                </div>
            </div>
        </div>
        <!-- Panduan dan Kartu Anggota -->
        <section id="our-blog">
            <div class="padding_m card">
                <div class="row px-5 py-5">
                    <!-- Kartu Anggota -->
                    <template v-if="imageUrl && profile">
                    <div class="member-card">
                        <div class="card-body">

                            <div class="member-photo">
                                <img :src="imageUrl" alt="Kartu Anggota" />
                            </div>
                            <div class="valid-until">
                                <span>Disahkan tanggal:</span>
                                <br><strong>{{ new Date(profile.created_at).toLocaleDateString('id-ID', {
                                    year:
                                        'numeric', month: 'long', day: 'numeric' }) }}</strong>
                            </div>
                            <div class="col-md-8">
                                <div class="member-details">
                                    <strong>{{ profile.name }}</strong>
                                    <br>
                                    <strong>{{ profile.nomember }}</strong>
                                </div>
                            </div>

                            <div class="qr-code">
                                <div v-html="qrCode"> </div>
                            </div>

                        </div>
                    </div>
                </template>


                    <template v-if="!imageUrl && profile">
                            <div class="member-default">
                                <img :src="'/assets/images/editcard.png'" alt="Kartu Anggota" />
                            </div>
                        </template>

                    <!-- Panduan Upload -->
                    <div class="col panduan-container ms-5">
                        <h4 class="panduan-title">📸 Panduan Update Foto Kartu Anggota</h4>
                        <ul class="panduan-list">
                            <li>
                                <i class="fa fa-dot-circle-o panduan-icon"></i>
                                Gunakan foto potret dengan background transparan dan pose badan sedikit condong ke
                                bagian kanan.
                            </li>
                            <li>
                                <i class="fa fa-dot-circle-o panduan-icon"></i>
                                Pastikan pencahayaan dan kualitas foto baik, gambar tidak blur.
                            </li>
                            <li>
                                <i class="fa fa-dot-circle-o panduan-icon"></i>
                                <fs-6>Gunakan resolusi <strong> 450x575 pixel </strong> untuk foto yang Anda upload.</fs-6>
                            </li>
                            <li>
                                <i class="fa fa-dot-circle-o panduan-icon"></i>
                                <fs-6> Gunakan format <strong> PNG </strong> dengan ukuran file maksimum <strong>2MB</strong>.</fs-6>
                            </li>
                        </ul>
                        <div class="upload-btn-container">
                            <button v-if="!imageUrl" @click="triggerFileInput" class="btn style-btn">
                                <i class="fa fa-upload"></i> Upload Foto
                            </button>
                                <input ref="fileInput" type="file" class="file-input ms-3" @change="updateImage"
                                    accept=".jpg, .jpeg, .png" />
                                    <button v-if="imageUrl" @click="saveImage" class="btn style-btn">
                            <i class="fa fa-save"></i> Simpan
                            </button>
                            <button v-if="imageUrl" @click="cancelUpload" class="btn style-btn ms-3">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            </div>

                    </div>

                </div>
            </div>
        </section>
    </div>
</template>

<script>
// Import Layout
import LayoutUser from '../../../Layouts/User.vue';
import MemberCard from '../../../Components/User/MemberCard.vue';

// Import Head and Link from router.js
import { Head, Link } from '@inertiajs/vue3';

// Import Vue features
import { ref } from 'vue';

// Import SweetAlert2
import Swal from 'sweetalert2';

export default {
    // Layout
    layout: LayoutUser,

    // Register Components
    components: {
        Head,
        Link,
        MemberCard,
    },

    // Props
    props: {
        errors: Object,
        session: Object,
        profile: Object,
        errors: Object,
        qrCode: String,
    },

    // Composition API
    setup() {

        // Reactive References
        const fileInput = ref(null); // File input reference
        const imageUrl = ref(''); // Displayed image URL

        // Method to trigger file input
        const triggerFileInput = () => {
            fileInput.value.click();
        };

         // Method to trigger file input
        const cancelUpload = () => {
            imageUrl.value = '';
        };

        // Method to handle image upload
        const updateImage = (event) => {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type and size
            const validTypes = ['image/jpeg', 'image/png'];
            if (!validTypes.includes(file.type)) {
            Swal.fire('Error', 'Format gambar harus JPG atau PNG.', 'error');
            return;
            }
            if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'Ukuran file maksimum adalah 2MB.', 'error');
            return;
            }

            // Convert image to URL for preview
            const reader = new FileReader();
            reader.onload = (e) => {
            imageUrl.value = e.target.result;
            // Anda bisa mengirim data ke server di sini jika diperlukan
            };
            reader.readAsDataURL(file);
        };

        const saveImage = async () => {
            if (!imageUrl.value) {
            console.log('Tidak ada gambar untuk disimpan.');
            Swal.fire('Error', 'Pilih gambar terlebih dahulu.', 'error');
            return;
            }

            // Buat objek FormData
            const formData = new FormData();

            // Ambil file dari input dan tambahkan ke FormData
            const fileInputElement = fileInput.value; // Akses elemen file input
            const file = fileInputElement.files[0]; // Ambil file yang dipilih
            if (!file) {
            console.log('File input kosong.');
            Swal.fire('Error', 'Pilih gambar terlebih dahulu.', 'error');
            return;
            }

            formData.append('image', file); // Tambahkan file ke FormData

            // Kirim data ke server
            console.log('Mengirim gambar ke server...');
            const response = await fetch('/user/member-card/update', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData,
            });

            if (response.ok) {
            Swal.fire('Sukses', 'Foto kartu anggota berhasil diupdate.', 'success').then(() => {
                window.location.href = '/user/member-card';
            });
            } else {
            Swal.fire('Error', 'Gagal mengupdate foto kartu anggota.', 'error');
            }
        };


        // Return to Template
        return {
            fileInput,
            imageUrl,
            triggerFileInput,
            updateImage,
            cancelUpload,
            saveImage
        };
    },
};
</script>

<style scoped>
.member-default {
    position: relative;
    width: 441px;
    height: 754px;
    box-shadow: 10px 10px 10px 10px rgba(0, 0, 0, 0.5);
    background-color: #ffffff; /* Tambahan background jika gambar belum dimuat */
    background-size: cover;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center; /* Center the content */
    overflow: hidden;
    padding: 20px;
    border-radius: 40px;
}

.member-default img {
    object-fit: cover;
    border-radius: 20px;
}

.member-card {
    position: relative;
    width: 441px;
    height: 754px;
    box-shadow: 10px 10px 10px 10px rgba(0, 0, 0, 0.5);
    background: url('/assets/images/bg-card-2.png') no-repeat center;
    background-size: cover;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    /* Center the content */
    align-items: center;
    overflow: hidden;
    padding: 20px;
    border-radius: 40px;
}

.card-body {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    border: 0;
    /* Set border to 0 */
    width: 100%;
    padding: 10px;
}

.member-photo {
    position: absolute;
    width: 350px;
    height: 470px;
    /* Set a fixed height to ensure consistent placement */
    margin: 0;
    padding: 0;
    top: 200%;
    /* Adjusted to move the photo upwards */
    right: 30px;
    transform: translateY(-50%);
    /* Center the photo vertically */
    overflow: hidden;
    /* Ensure the image fits within the container */
}

.member-photo img {
    position: absolute;
    width: 100%;
    height: 100%;
    /* Ensure the image covers the entire container */
    object-fit: cover;
    /* Maintain aspect ratio and cover the container */
    border-radius: 5px;
}


.valid-until {
    position: absolute;
    bottom: 50px;
    right: 10px;
    font-size: 14px;
    color: rgb(10, 2, 56);
    writing-mode: vertical-rl;
    text-orientation: mixed;
    text-align: left;
    white-space: nowrap;
}

.member-details {
    text-align: center;
    margin: 0;
    padding: 0;
    width: 100%;
    position: absolute;
    font-size: 20px;
    right: 0;
    top: 1400%;
    color: #000000;
}


.qr-code {
    display: flex;
    position: absolute;
    right: 7.5%;
    top: 973%;
    border: 3px solid #ccc;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.15);
    /* Slight shadow to give raised look */
    background: rgba(255, 255, 255, 0.8);
    /* Slight transparency for an emerging look */
    border-radius: 5px;

}

.panduan-container {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: left;
}

/* Title styling */
.panduan-title {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

/* List styling */
.panduan-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.panduan-list li {
    margin-bottom: 10px;
    font-size: 16px;
    color: #555;
    display: flex;
    align-items: center;
}

/* Icon styling */
.panduan-icon {
    color: #4caf50;
    /* Green check icon */
    font-size: 18px;
    margin-right: 10px;
}

/* Upload button container */
.upload-btn-container {
    margin-top: 20px;
    text-align: center;
}

/* Button styling */
.style-btn {
    background: #007bff;
    color: white;
    font-size: 16px;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.style-btn i {
    margin-right: 5px;
}

.style-btn:hover {
    background: #0056b3;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

/* File input hidden */
.file-input {
    display: none;
}
</style>
