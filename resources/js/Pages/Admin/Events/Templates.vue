                <template>

                    <Head>
                        <title>Template</title>
                    </Head>
                    <div class="px-5 shadow padding">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-1 col-12 mb-2">
                                            <Link :href="`/admin/events/${event_id}/certificates`" class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                                class="fa fa-arrow-left"></i>
                                            Kembali</Link>
                                        </div>
                                <div class="row">
                                    <h3 class="text-center">Template Sertifikat</h3>
                                    <form @submit.prevent="submit" enctype="multipart/form-data">
                                        <div class="row py-4">
                                            <div class="col-md-4">
                                                <span class="text-black">Title</span>
                                                <div class="form-group mt-1 mb-4">
                                                    <input type="text" class="form-control" v-model="form.title">
                                                </div>
                                                <div v-if="errors.title" class="alert alert-danger mt-2">
                                                    {{ errors.title }}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="text-black">Image</span>
                                                <div class="form-group mt-1 mb-4">
                                                    <input type="file" class="form-control" @change="updateImage" accept=".pdf" >
                                                </div>
                                                <div v-if="errors.image" class="alert alert-danger mt-2">
                                                    {{ errors.image }}
                                                </div>
                                                <div v-if="errors[0]" class="alert alert-danger mt-2">
                                                        {{ errors[0] }}
                                                    </div>
                                            </div>

                                            <div class="col-md-11 text-center">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-12">
                                <div class="card border-0 shadow">
                                    <div class="card-body">

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                                <thead class="thead-dark">
                                                    <tr class="border-0 text-center">
                                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                                        <th class="border-0">Title</th>
                                                        <th class="border-0">Image</th>
                                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <div class="mt-2"></div>
                                                <tbody>
                                                    <tr v-for="(template, index) in templates.data" :key="index">
                                                        <td class="fw-bold text-center">{{ ++index + (templates.current_page - 1) *
                                                            templates.per_page }}</td>

                                                        <td>{{ template.title }}</td>
                                                        <td style="width: 80px;">
                                                            <a class="btn btn-sm btn-primary border-0 me-1" data-fancybox="" :href="getImageUrl(template.image)"><i class="fa fa-eye"></i></a>
                                                            </td>

                                                        <td class="text-center">
                                                            <button @click.prevent="destroy(template.id)"
                                                                class="btn btn-sm btn-danger border-0 me-2"><i class="fa fa-trash"
                                                                    title="hapus"></i></button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <Pagination :links="templates.links" align="end" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <script>
                //import layout
                import LayoutAdmin from '../../../Layouts/Admin.vue';

                //import component pagination
                 import Pagination from '../../../Components/Pagination.vue';

                //import Heade and Link from Inertia
                import {
                    Head,
                    Link,
                } from '@inertiajs/vue3';

                import { ref } from 'vue';

                 //import inertia adapter
                import { router } from '@inertiajs/vue3';


                 //import sweet alert2
                 import Swal from 'sweetalert2';

                export default {
                    //layout
                    layout: LayoutAdmin,

                    //register component
                    components: {
                        Head,
                        Link,
                        Pagination

                    },

                    //props
                    props: {
                        errors: Object,
                        event_id: Number,
                        templates: Object,

                    },

                    //inisialisasi composition API
                    setup() {

                        const form = ref({
                            title: '',
                            image: '',
                            status: '',

                        });




                        //define method destroy
                        const destroy = (id) => {
                            Swal.fire({
                                title: 'Apakah Anda yakin?',
                                text: "Anda tidak akan dapat mengembalikan ini!",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete it!'
                            })
                                .then((result) => {
                                    if (result.isConfirmed) {

                                        router.delete(`/admin/events/certificates/templates/${id}`);

                                        Swal.fire({
                                            title: 'Deleted!',
                                            text: 'Media Berhasil Dihapus!.',
                                            icon: 'success',
                                            timer: 2000,
                                            showConfirmButton: false,
                                        });
                                    }
                                })
                        }

                        // Method to get the URL of the document
                        const getImageUrl = (imageName) => {
                            return `/storage/${imageName}`;
                        }


                        const submit = () => {
                            const formData = new FormData();
                            formData.append('title', form.value.title);
                            formData.append('image', form.value.image);
                            formData.append('status', form.value.status);
                            router.post('/admin/events/certificates/templates/store', formData);
                        };

                        const updateImage = (event) => {
                            form.value.image = event.target.files[0];
                        };


                        //return
                        return {
                            destroy,
                            getImageUrl,
                            form,
                            submit,
                            updateImage

                        }
                    }



                };
                </script>

<style>
.image {
    display: flex;
    justify-content: center; /* Untuk membuat gambar berada di tengah secara horizontal */
    align-items: center; /* Untuk membuat gambar berada di tengah secara vertikal */
    height: 200px; /* Atur tinggi sesuai kebutuhan Anda */
}

.image img {
    max-width: 100%;
    max-height: 100%;
    /* Anda dapat menyesuaikan properti CSS untuk gambar sesuai kebutuhan Anda */
}

</style>
