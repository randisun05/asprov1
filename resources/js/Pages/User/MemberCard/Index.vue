<template>

    <Head>
        <title>Kartu Anggota</title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    </Head>
    <div class="px-5 shadow padding scalable-container">
        <div class="row">
            <div class="col-md-12">
                <div class="row"></div>
            </div>
        </div>

        <!-- Our Blogs -->
        <div class="row text-center"></div>
        <section id="our-blog">
            <div class="padding_m card">
                <div class="row">
                    <div class="col-12">
                        <!-- Row utama, dibagi menjadi kolom -->
                        <div class="row px-5 py-5">
                            <!-- Kolom untuk MemberCard -->
                            <div class="col-md-4 card-member-container">
                                <MemberCard :profile="profile" :foto="foto" :qrCode="qrCode" />
                            </div>

                            <!-- Kolom untuk BackCard -->
                            <div class="col-md-4">
                                <BackCard></BackCard>
                            </div>

                            <!-- Kolom untuk informasi dan tombol -->
                           <!-- Kolom untuk informasi dan tombol -->
                            <div class="col-md-4">
                                <h3 class="mb-4" style="font-size: 28px; font-weight: bold; color: #333;">Kartu Anggota ASPRO SDM Aparatur</h3>
                                <h4 style="font-size: 18px; color: #777; line-height: 1.6;">
                                    Adalah tanda pengenal resmi bagi anggota Asosiasi Profesi Sumber Daya Manusia Aparatur (ASPRO SDM Aparatur).
                                </h4>
                                <br>
                                <h4 style="font-size: 18px; color: #777; line-height: 1.6;">
                                    Tautan alamat profile keanggotaan Anda adalah <a :href="qrLink" target="_blank">{{ qrLink }}</a>
                                </h4>
                                <div class="d-flex flex-column align-items-center mt-5">
                                    <div class="d-flex justify-content-between mb-3" style="width: 100%;">
                                        <button
                                            @click="downloadCard"
                                            class="btn btn-lg btn-outline-secondary shadow-sm"
                                            style="border-radius: 8px; background-color: #007bff; color: white; font-weight: bold; font-size: 18px; padding: 15px 30px; width: 48%; transition: all 0.3s ease;">
                                            <i class="fa fa-arrow-down fa-2x me-2" aria-hidden="true"></i>
                                            <span>Download</span>
                                        </button>
                                        <button @click="generateQRCode" class="btn btn-lg btn-outline-secondary shadow-sm" style="border-radius: 8px; background-color: #007bff; color: white; font-weight: bold; font-size: 18px; padding: 15px 30px; width: 48%; transition: all 0.3s ease;">
                                            <i class="fa fa-qrcode fa-2x me-2" aria-hidden="true"></i>
                                            <span>Download QR Code</span>
                                        </button>
                                    </div>
                                    <Link
                                        :href="`/user/member-card/edit`"
                                        class="btn btn-lg btn-outline-secondary shadow-sm"
                                        style="border-radius: 8px; background-color: #007bff; color: white; font-weight: bold; font-size: 18px; padding: 15px 30px; width: 48%; transition: all 0.3s ease;">
                                        <i class="fa fa-camera-retro fa-2x me-2" aria-hidden="true"></i>
                                        <span>Ubah Foto</span>
                                    </Link>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Our Blogs Ends-->
    </div>
    <iframe id="downloadFrame" style="visibility: hidden;"></iframe>

</template>


<script>
//import layout
import LayoutUser from '../../../Layouts/User.vue';

//import component membercard
import MemberCard from '../../../Components/User/MemberCard.vue';
import BackCard from '../../../Components/User/MemberCardBack.vue';

//import Head and Link from Inertia
import { Head, Link } from '@inertiajs/vue3';

//import ref from vue
import { ref, reactive } from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

//import sweet alert2
import Swal from 'sweetalert2';

export default {
    //layout
    layout: LayoutUser,

    //register component
    components: {
        Head,
        Link,
        MemberCard,
        BackCard
    },

    //props
    props: {
        errors: Object,
        profile: Object,
        foto: Object,
        qrCode: String,
        qrLink: String
    },

    //inisialisasi composition API
    setup() {
        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        //define method search
        const changePhoto = () => {
            router.get('/user/member-card/edit', {
                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        const downloadCard = async () => {
    // Select the iframe
    const iframe = document.getElementById('downloadFrame');

    // URL untuk mendownload kartu anggota dan backcard
    const downloadUrl = '/user/member-card/download';   // Untuk render di iframe
    const downloadUrlBack = '/user/member-card/backcard'; // Untuk download backcard.png

    // Set the src of iframe to trigger rendering
    iframe.src = downloadUrl;

    // Add an event listener to detect when the iframe is loaded (rendering is done)
    iframe.onload = () => {
        console.log('Rendering completed in iframe, starting download of backcard...');
        // Optionally trigger download logic here if needed for the card rendering
    };

    try {
        // Fetch the backcard image and trigger the download
        const responseBack = await fetch(downloadUrlBack, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!responseBack.ok) {
            throw new Error('Failed to fetch backcard image.');
        }

        // Get the blob of the image to initiate download
        const blob = await responseBack.blob();
        const url = URL.createObjectURL(blob);

        // Create an anchor element to trigger the download
        const a = document.createElement('a');
        a.href = url;
        a.download = 'kartu-anggota-belakang.png'; // Set the file name for the download
        a.click(); // Programmatically click the anchor to start the download
        URL.revokeObjectURL(url); // Clean up the object URL after download
    } catch (error) {
        console.error('Error during file download:', error);
    }
};

const generateQRCode = async () => {
    try {
        const response = await fetch('/user/member-card/qrcode', {
            method: 'GET',
            headers: {
                'Content-Type': 'image/svg+xml',
            },
        });

        if (!response.ok) {
            throw new Error(`Failed to fetch QR Code. Status: ${response.status}`);
        }

        // Respons langsung berupa SVG, maka kita unduh
        const blob = await response.blob();
        const url = URL.createObjectURL(blob);

        // Buat elemen anchor untuk memicu unduhan
        const a = document.createElement('a');
        a.href = url;
        a.download = 'qrcode.svg';
        a.click();

        // Bersihkan URL setelah selesai
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error during QR Code download:', error);
        Swal.fire('Error', 'Gagal mengunduh QR Code. Coba lagi nanti.', 'error');
    }
};

        //return
        return {
            getImageUrl,
            changePhoto,
            downloadCard,
            generateQRCode
        }
    }
}
</script>

<style scoped>
.scalable-container {
    transform: scale(1);
    /* Prevent scaling */
    transform-origin: top left;
    /* Prevent scaling */
}

.card-member-container {
    width: 441px;
    height: 740px;
    margin-right: 20px;
}
</style>
