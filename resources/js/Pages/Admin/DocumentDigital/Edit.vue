                <template>

                    <Head>
                        <title>Buat Dokumen Digital</title>
                    </Head>
                    <div class="container padding px-5">
                        <div class="row mt-1">
                            <div class="col-md-12">
                                <div class="card border-0 shadow">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row py-4">
                                                    <div class="col-md-2 col-12 mb-2">
                                                        <Link href="/admin/docudigi"
                                                            class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                                            class="fa fa-arrow-left"></i>
                                                        Kembali</Link>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="text-center">Buat Dokumen Digital</h3>
                                        <form @submit.prevent="submit" enctype="multipart/form-data">

                                            <div class="row py-2 ms-2">
                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        Perihal
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <input type="text" class="form-control"
                                                            placeholder="Masukan Perihal/Nomor Surat" v-model="form.perihal">
                                                    </div>
                                                    <div v-if="errors.perihal" class="alert alert-danger mt-2">
                                                        {{ errors.perihal }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        Jenis
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <select class="form-select" v-model="form.jenis">
                                                            <option value="" disabled selected>Pilih salah satu opsi
                                                            </option>
                                                            <option value="1">Surat</option>
                                                            <option value="2">Berita Acara</option>
                                                            <option value="3">Laporan</option>
                                                        </select>
                                                    </div>
                                                    <div v-if="errors.jenis" class="alert alert-danger mt-2">
                                                        {{ errors.jenis }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        Speciment
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <textarea class="form-control" placeholder="Masukan Speciment"
                                                            v-model="form.speciment"></textarea>
                                                    </div>
                                                    <div v-if="errors.speciment" class="alert alert-danger mt-2">
                                                        {{ errors.speciment }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        NIP TTD
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <input type="text" class="form-control" placeholder="Masukan NIP Penandatangan"
                                                            v-model="form.nipttd">
                                                    </div>
                                                    <div v-if="errors.nipttd" class="alert alert-danger mt-2">
                                                        {{ errors.nipttd }}
                                                    </div>
                                                    <span class="text-black">
                                                        Anchor
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <input type="text" class="form-control" placeholder="Masukan Achor"
                                                            v-model="form.anchor">
                                                    </div>
                                                    <div v-if="errors.anchor" class="alert alert-danger mt-2">
                                                        {{ errors.anchor }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        NIP Paraf
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <input type="text" class="form-control" placeholder="Masukan NIP Paraf"
                                                            v-model="form.nipparaf">
                                                    </div>
                                                    <div v-if="errors.nipparaf" class="alert alert-danger mt-2">
                                                        {{ errors.nipparaf }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        Tujuan
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <input type="text" class="form-control" placeholder="Masukan Tujuan Instansi"
                                                            v-model="form.tujuan">
                                                    </div>
                                                    <div v-if="errors.tujuan" class="alert alert-danger mt-2">
                                                        {{ errors.tujuan }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        Deskripsi
                                                    </span>
                                                    <div class="form-group mt-1 mb-4">
                                                        <textarea type="text" class="form-control" placeholder="Masukan Deskripsi"
                                                            v-model="form.description"></textarea>
                                                    </div>
                                                    <div v-if="errors.description" class="alert alert-danger mt-2">
                                                        {{ errors.description }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-sm-6">
                                                    <span class="text-black">
                                                        Dokumen
                                                    </span>
                                                    <div class="form-group mt-1">
                                                        <input type="file" class="form-control" placeholder="Masukan Gambar/Foto"
                                                            @change="updateDocument" accept=".pdf">
                                                    </div>
                                                    <div v-if="errors.document" class="alert alert-danger mt-2">
                                                        {{ errors.document }}
                                                    </div>
                                                    <div v-if="errors[0]" class="alert alert-danger mt-2">
                                                        {{ errors[0] }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row d-flex justify-content-center">
                                                <div class="col-md-2">
                                                    <button type="submit"
                                                        class="btn btn-md btn-primary border-0 shadow me-2">Simpan</button>
                                                    <button type="reset" class="btn btn-md btn-warning border-0 shadow">Reset</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <script>
                //import layout
                import LayoutAdmin from '../../../Layouts/Admin.vue';

                //import Heade and Link from Inertia
                import {
                    Head,
                    Link
                } from '@inertiajs/vue3';

                //import tinyMCE
                import Editor from '@tinymce/tinymce-vue';

                //import ref from vue
                import {
                    ref, reactive,
                } from 'vue';

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
                        Editor,
                    },

                    //props
                    props: {
                        errors: Object,
                        docu: Object,

                    },


                    //inisialisasi composition API
                    setup(props) {

                        //define form state
                        const form = reactive({
                            perihal: props.docu.perihal,
                            jenis: props.docu.jenis,
                            speciment: props.docu.speciment,
                            nipttd: props.docu.nipttd,
                            anchor: props.docu.anchor,
                            nipparaf: props.docu.nipparaf,
                            tujuan: props.docu.tujuan,
                            document: props.docu.document,
                            description: props.docu.description,
                        });


                        //submit method
                        const submit = () => {

                            //send data to server
                            router.post('/admin/docudigi/', {

                                //data
                                perihal: form.perihal,
                                jenis: form.jenis,
                                speciment: form.speciment,
                                nipttd: form.nipttd,
                                anchor: form.anchor,
                                nipparaf: form.nipparaf,
                                tujuan: form.tujuan,
                                document: form.document,
                                description: form.description,

                            }, {
                                onSuccess: () => {
                                    //show success alert
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Document Berhasil Diupdate.',
                                        icon: 'success',
                                        showConfirmButton: false,
                                        timer: 2000
                                    });
                                },
                            });
                        }

                        const updateDocument = (event) => {
                            form.document = event.target.files[0];
                        };


                        //return
                        return {
                            form,
                            submit,
                            updateDocument


                        }
                    }
                }

                </script>

                <style>
                .text-black {
                    color: black;
                }
                </style>
