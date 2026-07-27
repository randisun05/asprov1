<template>
    <!-- header -->
    <header class="site-header" :class="{ 'fixedmenu': isScrolled, 'transparent-bg': !isScrolled}">
        <nav class="navbar navbar-expand-lg  static-nav" >
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="/assets/images/logo2.png" alt="logo" style="width: 70%;">
                </a>
                <button class="navbar-toggler navbar-toggler-right collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#xenav">
                    <span> </span>
                    <span> </span>
                    <span> </span>
                </button>
                <div class="collapse navbar-collapse" id="xenav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item active">
                            <a class="nav-link pagescroll" href="/">Beranda</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown"
                                href="#tentang kita" role="button" aria-expanded="false">Tentang Kita</a>
                            <ul class="dropdown-menu pagescroll" aria-expanded="false">
                                <li><a class="dropdown-item" href="/tentang-aspro">Siapa Kita?</a></li>
                                <li><a class="dropdown-item" href="/ketua-umum">Ketua Umum</a></li>
                                <li><a class="dropdown-item" href="/struktur-organisasi">Struktur Organisasi</a></li>
                                <li><a class="dropdown-item" href="/visi-misi">Visi Misi</a></li>
                                <li><a class="dropdown-item" href="/sejarah">Sejarah Terbentuknya Aspro SDMA</a></li>
                                <li><a class="dropdown-item" href="/peraturan-organisasi">Peraturan Organisasi</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown" href="#program"
                                role="button" aria-expanded="false">Program</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/bidang-keanggotaan-dan-organisasi">Bidang
                                        Keanggotaan dan Organisasi</a></li>
                                <li><a class="dropdown-item" href="/bidang-pengembangan-kapasitas-insani">Bidang
                                        Pengembangan Kapasitas Insansi</a></li>
                                <li><a class="dropdown-item" href="/bidang-hukum-dan-advokasi">Bidang Hukum dan
                                        Advokasi</a></li>
                                <li><a class="dropdown-item" href="/bidang-hubungan-masyarakat-dan-kerja-sama">Bidang
                                        Hubungan Masyarakat dan Kerja Sama</a></li>
                                <li><a class="dropdown-item" href="/bidang-sumber-pendanaan-organisasi">Bidang Sumber
                                        Pendanaan Organisasi</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown" href="#publikasi"
                                role="button" aria-expanded="false">Publikasi</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/events">Kegiatan</a></li>
                                <li><a class="dropdown-item" href="/berita">Berita</a></li>
                                <li><a class="dropdown-item" href="/sdma-menulis">SDMA Menulis</a></li>
                                <li><a class="dropdown-item" href="/merchans">Merchandise</a></li>
                                <li><a class="dropdown-item" href="/data-anggota">Data Anggota</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown" href="#keanggotaan"
                                role="button" aria-expanded="false">Keanggotaan</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/registration">Daftar</a></li>
                                <li><a class="dropdown-item" href="/user/login">Masuk</a></li>
                                <li><a class="dropdown-item" href="/faq">FAQ</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown" href="#publikasi"
                                role="button" aria-expanded="false">Kontak Kami</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/kontak-kami">Kontak Kami</a></li>
                                <!-- <li><a class="dropdown-item" href="/hubungi-aspro">Hubungi Kami</a></li> -->
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <!--side menu open button-->
            <div class="dropdown" v-if="$page.props.auth.member">
                <a href="#" class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                    id="sidemenu_toggle">
                    <div class="media d-flex align-items-center">
                        <img class="avatar rounded-circle" alt="Image placeholder"
                            :src="`https://ui-avatars.com/api/?name=${$page.props.auth.member.name}&amp;background=4e73df&amp;color=ffffff&amp;size=40`">
                        <div class="media-body text-dark align-items-center d-none d-lg-block ms-2">
                            <span class="mb-0 font-small fw-bold text-gray-900">{{ $page.props.auth.member.name
                                }}</span>
                        </div>
                    </div>
                </a>
                <ul class="dropdown-menu" aria-labelledby="sidemenu_toggle">
                    <Link class="dropdown-item d-flex align-items-center" href="/user/dashboard" as="button">
                    Profile
                    </Link>
                    <Link class="dropdown-item d-flex align-items-center mt-2" href="/logout" method="POST" as="button">
                    Logout
                    </Link>
                </ul>
            </div>
        </nav>

        <!-- side menu -->
        <!-- <div class="side-menu">
      <div class="inner-wrapper">
         <span class="btn-close" id="btn_sideNavClose"><i></i><i></i></span>
         <nav class="side-nav">

            <ul class="navbar-nav w-100">
               <li class="nav-item active">
                  <a class="nav-link pagescroll" href="/">Beranda</a>
               </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#tentangkita" aria-expanded="false">Tentang Kita <i class="bi bi-chevron-down"></i></a>
                        <div class="collapse" id="tentangkita">
                           <ul class="navbar-nav ms-4">
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll mt-0" href="/siapa-kita?" style="font-size: 1rem;">Siapa Kita?</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/ketua-umum" style="font-size: 1rem;">Ketua Umum</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/struktur-organisasi" style="font-size: 1rem;">Struktur Organisasi</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/visi-misi" style="font-size: 1rem;">Visi Misi</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/sejarah-terbentuknya-aspro-sdma" style="font-size: 1rem;">Sejarah Terbentuknya Aspro SDMA</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/peraturan-organisasi" style="font-size: 1rem;">Peraturan Organisasi</a>
                                 </li>
                           </ul>
                        </div>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#program" aria-expanded="false">Program <i class="bi bi-chevron-down"></i></a>
                        <div class="collapse" id="program">
                           <ul class="navbar-nav ms-4">
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll mt-0" href="/pelatihan" style="font-size: 1rem;">Bidang Keanggotaan dan Organisasi</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/sertifikasi" style="font-size: 1rem;">Bidang Pengembangan Kapasitas Insani</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/kegiatan-aspro-sdma" style="font-size: 1rem;">Bidang Hukum dan Advokasi</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/kegiatan-aspro-sdma" style="font-size: 1rem;">Bidang Hukum Masyarakat dan Kerja Sama</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/kegiatan-aspro-sdma" style="font-size: 1rem;">Bidang Sumber Pendanaan Organisasi</a>
                                 </li>
                           </ul>
                        </div>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#publikasi" aria-expanded="false">Publikasi <i class="bi bi-chevron-down"></i></a>
                        <div class="collapse" id="publikasi">
                           <ul class="navbar-nav ms-4">
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll mt-0" href="/events" style="font-size: 1rem;">Kegiatan</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/berita" style="font-size: 1rem;">Berita</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/cerita" style="font-size: 1rem;">Cerita</a>
                                 </li>
                           </ul>
                        </div>
                     </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#anggota" aria-expanded="false">keanggotaan <i class="bi bi-chevron-down"></i></a>
                        <div class="collapse" id="anggota">
                           <ul class="navbar-nav ms-4">
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll mt-0" href="/registration" style="font-size: 1rem;">Daftar</a>
                                 </li>
                                 <li class="nav-item mt-0">
                                    <a class="nav-link pagescroll" href="/user/login" style="font-size: 1rem;">Masuk</a>
                                 </li>
                           </ul>
                        </div>
                     </li>

                     <li class="nav-item">
                        <a class="nav-link pagescroll" href="/kotak-kami">Kontak Kami</a>
                     </li>
            </ul>
         </nav>

         <div class="side-footer w-100">
            <ul class="social-icons-simple white top40">
            <li><a href="javascript:void(0)"><i class="fa fa-facebook"></i> </a> </li>
            <li><a href="javascript:void(0)"><i class="fa fa-instagram"></i> </a> </li>
            <li><a href="javascript:void(0)"><i class="fa fa-twitter"></i> </a> </li>
         </ul>
         <p class="whitecolor">&copy; 2024 Aspro SDMA</p>
         </div>
      </div>
   </div>
   <a id="close_side_menu" href="javascript:void(0);"></a> -->
        <!-- End side menu -->
    </header>
    <!-- header -->
</template>

<script>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
//import Link
import { Link,  } from '@inertiajs/vue3';



export default {
    //register component
    components: {
        Link,
    },

    data() {
        return {
            dropdowns: {
                home: false,
                profile: false,
                program: false,
                publikasi: false,
                keanggotaan: false,
                kontak: false,
            },
            dropdownItems: {
                profile: ["Tentang Aspro", "Ketua Umum", "Struktur Organisasi", "Visi Misi", "Sejarah Terbentuknya Aspro", "Peraturan Organisasi"],
                program: ["Pelatihan", "Sertifikasi", "Kegiatan Aspro"],
                publikasi: ["Pengumuman", "Artikel", "Berita", "Media"],
                keanggotaan: ["Login", "Registrasi"],
            },
            isScrolled: false, // New property to track scroll state
        };
    },
    methods: {
        toggleDropdown(dropdown) {
            this.dropdowns[dropdown] = !this.dropdowns[dropdown];
        },

    },
    mounted() {
        const handleScroll = () => {
            this.isScrolled = window.scrollY > 0;
        };
        window.addEventListener('scroll', handleScroll);
        onUnmounted(() => {
            window.removeEventListener('scroll', handleScroll);
        });
    },




}
</script>


<style>
/* CSS for red background */
/* CSS for red background */


/* CSS for fixed header */
.fixedmenu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    transition: background-color 0.3s ease-in-out; /* Smooth transition for background color */
}



</style>
