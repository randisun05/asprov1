    <template>
    <!-- header -->

    <header class="site-header userbg">
        <nav class="navbar navbar-expand-lg  static-nav">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    <img src="/assets/images/logo.png" alt="logo" style="width: 70%;">
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
                                    <li><a class="dropdown-item" href="/struktur-organisasi">Struktur Organisasi</a>
                                    </li>
                                    <li><a class="dropdown-item" href="/visi-misi">Visi Misi</a></li>
                                    <li><a class="dropdown-item" href="/sejarah">Sejarah Terbentuknya Aspro SDMA</a>
                                    </li>
                                    <li><a class="dropdown-item" href="/peraturan-organisasi">Peraturan Organisasi</a>
                                    </li>
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
                                    <li><a class="dropdown-item"
                                            href="/bidang-hubungan-masyarakat-dan-kerja-sama">Bidang Hubungan Masyarakat
                                            dan Kerja Sama</a></li>
                                    <li><a class="dropdown-item" href="/bidang-sumber-pendanaan-organisasi">Bidang
                                            Sumber Pendanaan Organisasi</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown"
                                    href="#publikasi" role="button" aria-expanded="false">Publikasi</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/events">Kegiatan</a></li>
                                <li><a class="dropdown-item" href="/berita">Berita</a></li>
                                <li><a class="dropdown-item" href="/merchans">Merchandise</a></li>
                                <li><a class="dropdown-item" href="/data-anggota">Data Anggota</a></li>

                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link pagescroll dropdown-toggle" data-bs-toggle="dropdown"
                                    href="#keanggotaan" role="button" aria-expanded="false">Keanggotaan</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="/registration">Daftar</a></li>
                                    <li><a class="dropdown-item" href="/login">Masuk</a></li>
                                    <li><a class="dropdown-item" href="/faq">FAQ</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pagescroll" href="/#sapa-kita">Kontak Kami</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--notification bell-->
                <div class="dropdown me-2">
                    <a href="#" class="btn position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        id="notification_toggle">
                        <i class="fa fa-bell fa-lg" aria-hidden="true"></i>
                        <span v-if="unreadCount > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size:0.6rem;">
                            {{ unreadCount > 9 ? '9+' : unreadCount }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notification_toggle" style="min-width: 300px;">
                        <li v-if="latestNotifications.length === 0" class="dropdown-item-text text-muted">Belum ada notifikasi.</li>
                        <li v-for="notification in latestNotifications" :key="notification.id">
                            <Link class="dropdown-item" :class="{ 'fw-bold': !notification.is_read }"
                                :href="`/user/notifications/${notification.id}/read`" method="POST" as="button">
                            {{ notification.title }}
                            </Link>
                        </li>
                        <li><hr class="dropdown-divider" v-if="latestNotifications.length > 0"></li>
                        <li>
                            <Link class="dropdown-item text-center" href="/user/notifications">Lihat semua notifikasi</Link>
                        </li>
                    </ul>
                </div>
                <!--side menu open button-->
                <div class="dropdown">
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
                        <Link class="dropdown-item d-flex align-items-center" href="/user/setting" method="GET"
                            as="button">
                        Setting
                        </Link>
                        <Link class="dropdown-item d-flex align-items-center mt-2" href="/logout" method="POST"
                            as="button">
                        Logout
                        </Link>

                    </ul>
                </div>
        </nav>
    </header>

</template>

<script>
//import Link
import { Link } from '@inertiajs/vue3';

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

        };
    },

    computed: {
        unreadCount() {
            return this.$page.props.notifications?.unreadCount ?? 0;
        },
        latestNotifications() {
            return this.$page.props.notifications?.latest ?? [];
        },
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
    transition: background-color 0.3s ease-in-out;
    /* Smooth transition for background color */
}

.userbg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    background: -webkit-linear-gradient(90deg, #423f9c, #862359);
    background: -webkit-gradient(linear, left top, right top, color-stop(#423f9c), color-stop(#862359));
    background: -webkit-linear-gradient(left, #423f9c, #862359);
    background: -o-linear-gradient(left, #423f9c, #862359);
    background: -webkit-gradient(linear, left top, right top, from(#423f9c), to(#862359));
    background: linear-gradient(90deg, #423f9c, #862359);
    z-index: 100000;
}
</style>
