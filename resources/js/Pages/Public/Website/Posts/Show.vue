<template>

    <Head>
        <title>{{ title }}</title>
    </Head>

    <!--page Header-->
    <section class="page-header parallaxie padding_top center-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-titles text-center">
                        <h2 class="whitecolor font-light bottom30">{{ title }}</h2>
                        <ul class="breadcrumb justify-content-center">
                            <li class="breadcrumb-item"><a href="/">Beranda</a></li>
                            <li class="breadcrumb-item"><a href="/berita">Berita</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3">
                </div>
            </div>
        </div>
    </section>
    <!--page Header ends-->


    <!-- Our Blogs -->
    <section id="our-blog">
        <div class="container mb-5 mt-5">
            <div class="row mt-1">
                <div class="col-md-12">
                    <div class="card border-0 shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row py-4">
                                        <div class="col-md-2 col-12 mb-2">
                                        <Link :href="post.category_id === 3 ? '/sdma-menulis' : '/berita'" class="btn btn-md btn-secondary border-0 shadow w-100" type="button">
                                            <i class="fa fa-arrow-left"></i> Kembali
                                        </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row px-5 py-3">
                                <h3 class="text-center">{{ post.title }}</h3>
                                <p class="mb-0 mt-2">Kategori: {{ post.category.title }}</p>
                                <p class="mb-0"><i class="fa fa-user me-2"></i> {{ post.member.name }}</p>
                                <p class="mb-0"><i class="fa fa-calendar me-2"></i> {{ post.publish_at }}</p>
                                <div class="image-container">
                                    <img v-if="post.image" :src="getImageUrl(post.image)" alt="Gambar" />
                                </div>
                                <div v-html="post.body" class="mt-4" style="text-align:justify;text-justify: "></div>
                                 <p class="mt-4" v-if="post?.document && post?.docstatus == '1'">Dokumen dapat didownload <a
                                    :href="getDocumentUrl(post.document)" download><u>disini.</u></a></p>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Our Blogs Ends-->

</template>

<script>
//import layout
import LayoutWebsite from '../../../../Layouts/Website.vue';

//import component pagination
import Pagination from '../../../../Components/Pagination.vue';

//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';

//import ref from vue
import {
    ref
} from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';

export default {

    //layout
    layout: LayoutWebsite,

    //register component
    components: {
        Head,
        Link,
        Pagination
    },

    //props
    props: {
        title: Object,
        errors: Object,
        post: Object,
    },

    //inisialisasi composition API
    setup() {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            router.get('/posts', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        // Method to get the URL of the document
        const getDocumentUrl = (documentName) => {
            return `/storage/${documentName}`;
        }

        //return
        return {
            search,
            handleSearch,
            getImageUrl,
            getDocumentUrl


        }
    }

}

</script>

<style>
.image-container {
    display: flex;
    justify-content: center;
    /* Untuk membuat gambar berada di tengah secara horizontal */
    align-items: center;
    /* Untuk membuat gambar berada di tengah secara vertikal */
    height: 400px;
    /* Atur tinggi sesuai kebutuhan Anda */
}

.image-container img {
    max-width: 100%;
    max-height: 100%;
    /* Anda dapat menyesuaikan properti CSS untuk gambar sesuai kebutuhan Anda */
}
</style>
