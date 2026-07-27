<template>
    <FlashMessages />
    <div class="main-layout">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <Link class="navbar-brand font-weight-bold" href="/"></Link>

                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user-circle me-1"></i> Akun
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu">
                            <li>
                                <Link class="dropdown-item py-2" href="/user/setting" method="GET" as="button">
                                    <i class="fa fa-cog me-2 text-muted"></i> Setting
                                </Link>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <Link class="dropdown-item py-2 text-danger" href="/logout" method="POST" as="button">
                                    <i class="fa fa-sign-out-alt me-2"></i> Logout
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="content-wrapper">
            <main class="container py-4">
                <slot />
            </main>
        </div>

        <Footer />
    </div>
</template>

<script>
import Footer from '../Components/Footer.vue';
import { Link } from '@inertiajs/inertia-vue3';
import FlashMessages from '../Components/FlashMessages.vue';

export default {
    components: {
        Footer,
        Link,
        FlashMessages
    },
}
</script>
