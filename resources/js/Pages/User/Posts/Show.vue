<template>

    <Head>
        <title>Preview Cerita</title>
    </Head>
    <div class="container padding">
        <div class="row mt-1">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row py-4">
                                    <div class="col-md-2 col-12 mb-2">
                                        <Link href="/user/posts" class="btn btn-md btn-primary border-0 shadow w-100"
                                            type="button"><i class="fa fa-arrow-left"></i>
                                        Kembali</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-center" style="">Preview Cerita/Artikel/Berita</h3>

                        <div class="row px-5 py-3">
                            <h3 class="text-center">{{ post.title }}</h3>
                            <p class="mb-0">Kategori: {{ post.category.title }}</p>
                            <p>Oleh: {{ post.member.name }} - Dipublikasi: {{ post.publish_at }}</p>
                            <div class="image-container">
                                <img v-if="post.image" :src="getImageUrl(post.image)" alt="Gambar" />
                            </div>
                            <div v-html="post.body" style="text-align:justify;text-justify: " class="mt-4"></div>
                             <p class="mt-4" v-if="post?.document && post?.docstatus == '1'">Dokumen dapat didownload <a
                                    :href="getDocumentUrl(post.document)" download><u>disini.</u></a></p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
//import layout
import LayoutUser from '../../../Layouts/User.vue';

//import Heade and Link from Inertia
import {
    Head,
    Link
} from '@inertiajs/vue3';


//import ref from vue
import {
    ref, reactive,
} from 'vue';

//import inertia adapter
import { router } from '@inertiajs/vue3';



export default {
    //layout
    layout: LayoutUser,

    //register component
    components: {
        Head,
        Link,
    },

    //props
    props: {
        errors: Object,
        post: {
            type: Object,
            default: null  // Default value is set to null
        }
    },


    //inisialisasi composition API
    setup() {

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

        const getDocumentUrl = (imageName) => {
            return `/storage/${imageName}`;
        }



        //return
        return {
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
