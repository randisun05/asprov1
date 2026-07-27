<template>
    <Head>
        <title>Sertifikat Saya</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-12 mb-2">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search" placeholder="masukkan kata kunci dan enter...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-md-12">
                <h3 class="mb-3">Sertifikat Saya</h3>
            </div>
        </div>

        <div v-if="datas.data.length" class="row g-3">
            <div class="col-md-4 col-sm-6" v-for="data in datas.data" :key="data.id">
                <div class="card border-0 shadow h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="fa fa-certificate fa-lg" aria-hidden="true"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ data.event?.title || data.category }}</h6>
                            <span class="text-muted small d-block">{{ data.no_certificate }}</span>
                            <span class="text-muted small d-block">{{ data.date }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center pb-3">
                        <button @click="downloadCard(data)" class="btn btnprimary btn-sm" type="button">
                            <i class="fa fa-download me-1" aria-hidden="true"></i> Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="row">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fa fa-certificate fa-3x mb-3 d-block" aria-hidden="true"></i>
                        Belum ada sertifikat. Sertifikat akan muncul di sini setelah Anda mengikuti kegiatan yang menerbitkan sertifikat.
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <Pagination :links="datas.links" align="end" />
        </div>
    </div>
    <iframe id="downloadFrame" style="visibility: hidden;"></iframe>
</template>

<script>
    //import layout
    import LayoutUser from '../../../Layouts/User.vue';

    //import component pagination
    import Pagination from '../../../Components/Pagination.vue';

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

    //import sweet alert2
    import Swal from 'sweetalert2';

    export default {
        //layout
        layout: LayoutUser,

        //register component
        components: {
            Head,
            Link,
            Pagination
        },

        //props
        props: {
            datas :Object,

        },

        //inisialisasi composition API
        setup(props) {

            //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get('/user/certificates', {

                    //send params "q" with value from state "search"
                    q: search.value,
                });
            }

            const downloadCard = async (data) => {
                try {
                    // Select the iframe
                    const iframe = document.getElementById('downloadFrame');

                    // URL to download the certificate
                    const downloadUrl = `/user/certificates/${data.id}`;

                    // Set the iframe source to the download URL
                    iframe.src = downloadUrl;

                    // Add an event listener to detect when the iframe is loaded
                    iframe.onload = () => {
                        console.log('Certificate download initiated.');
                    }
                } catch (error) {
                    console.error("Error downloading certificate:", error);
                    Swal.fire('Error', 'Failed to download certificate.', 'error');
                }
            };




            //return
            return {
                search,
                handleSearch,
                downloadCard
        }
    }
}

</script>

<style>
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
