<template>
<header class="site-header userbg">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.html">
          <img src="/assets/images/logo.png" alt="logo" style="width: 70%;">
          </a>
          <button class="navbar-toggler navbar-toggler-right collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#xenav">
             <span> </span>
             <span> </span>
             <span> </span>
          </button>
        </div>
        <!--side menu open button-->
        <div class="dropdown">
            <a href="#" class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="sidemenu_toggle">
                <div class="media d-flex align-items-center">
                    <img class="avatar rounded-circle" alt="Image placeholder" :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&amp;background=4e73df&amp;color=ffffff&amp;size=40`">
                        <div class="media-body text-dark align-items-center d-none d-lg-block ms-2">
                            <span class="mb-0 font-small fw-bold text-gray-900">{{ $page.props.auth.user.name }}</span>
                        </div>
                </div>
            </a>
                    <ul class="dropdown-menu" aria-labelledby="sidemenu_toggle">
                        <Link v-if="$page.props.auth.user.role === 'administrator'" href="/admin/setting" class="dropdown-item d-flex align-items-center" role="button">Setting
                        </Link>
                        <Link href="/admin/security/two-factor" class="dropdown-item d-flex align-items-center" role="button">Keamanan (2FA)
                        </Link>
                        <Link class="dropdown-item d-flex align-items-center" href="/logout" method="POST" as="button" :data="{role : 'admin'}">
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
        }
    }

</script>

<style>

/* CSS for fixed header */
.fixedmenu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
    transition: background-color 0.3s ease-in-out; /* Smooth transition for background color */
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
