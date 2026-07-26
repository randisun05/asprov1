<template>
     <nav class="navbar navbar-expand-md sidebar-nav mt-5">
      <div class="collapse navbar-collapse" id="xenav">
         <ul class="navbar-nav ms-auto">
            <li class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/management') }">
                    <Link href="/admin/management" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                            <!-- <i class="fa fa-tachometer fa-lg" aria-hidden="true"></i> -->
                            <i class="fa fa-tasks fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Management</span>
                    </span>
                    </Link>
                </li>

            <li class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/dashboard') }">
                    <Link href="/admin/dashboard" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                            <i class="fa fa-tachometer fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Dashboard</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'keanggotaan' || $page.props.auth.user.role === 'administrator'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/registration') }">
                    <Link href="/admin/registration" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-users fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Pendaftaran</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'keanggotaan' || $page.props.auth.user.role === 'administrator' || $page.props.auth.user.role === 'pendanaan' || $page.props.auth.user.role === 'humas'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/members') }">
                    <Link href="/admin/members" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-user fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Anggota</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'humas' || $page.props.auth.user.role === 'administrator'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/posts') }">
                    <Link href="/admin/posts" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-bullhorn fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Posts</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'humas' || $page.props.auth.user.role === 'administrator'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/events') }">
                    <Link href="/admin/events" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-calendar fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Events</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'humas' || $page.props.auth.user.role === 'administrator'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/medias') }">
                    <Link href="/admin/medias" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-photo fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Media</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'pendanaan' || $page.props.auth.user.role === 'administrator'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/merchans') }">
                    <Link href="/admin/merchans" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-tags fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Merchans</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'administrator' || $page.props.auth.user.role === 'sekretariat'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/docudigi') }">
                    <Link href="/admin/docudigi" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-file-text fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Dokumen Digital</span>
                    </span>
                    </Link>
                </li>

                <li v-if="$page.props.auth.user.role === 'administrator'" class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/achievements') }">
                    <Link href="/admin/achievements" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-trophy fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Achievements</span>
                    </span>
                    </Link>
                </li>

                <li class="nav-item" v-if="$page.props.auth.user.role === 'administrator' || $page.props.auth.user.role === 'sekretariat'" :class="{ 'active': $page.url.startsWith('/admin/archives') }">
                    <Link href="/admin/archives" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-archive fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Surat Masuk</span>
                    </span>
                    </Link>
                </li>

                <li class="nav-item" v-if="$page.props.auth.user.role === 'administrator' || $page.props.auth.user.role === 'sekretariat'" :class="{ 'active': $page.url.startsWith('/admin/archives') }">
                    <Link href="/admin/archives/inbox" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-inbox fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Disposisi</span>
                    </span>
                    </Link>
                </li>

                <li class="nav-item" v-if="$page.props.auth.user.role == 'administrator' || $page.props.auth.user.role == 'pendanaan' " :class="{ 'active': $page.url.startsWith('/admin/jurnals') }">
                    <Link href="/admin/jurnals" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-money fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Pencatatan Keuangan</span>
                    </span>
                    </Link>
                </li>

                <li class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/jurnals') }">
                    <Link href="/admin/jurnals/show" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-money fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Catatan Keuangan</span>
                    </span>
                    </Link>
                </li>


                 <li class="nav-item" :class="{ 'active': $page.url.startsWith('/admin/questions') }">
                    <Link href="/admin/questions" class="nav-link d-flex justify-content-between">
                    <span>
                        <span class="sidebar-icon">
                           <i class="fa fa-question-circle fa-lg" aria-hidden="true"></i>
                        </span>
                        <span class="sidebar-text ms-3">Soal</span>
                    </span>
                    </Link>
                </li>


         </ul>
      </div>
   </nav>

</template>

<script>

//import Link
import { Link } from '@inertiajs/inertia-vue3';

export default {

    //register components
    components: {
        Link
    },

}

</script>

<style>
</style>
