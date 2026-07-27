        <template>

            <Head>
                <title>Tambah Sertifikat</title>
            </Head>
            <div class="px-5 shadow padding">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">

                            <h3 class="text-center">Tambah Sertifikat</h3>
                            <div class="col-md-1 col-12 mb-2">
                                <Link :href="`/admin/events/${event.id}/certificates`"
                                    class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                    class="fa fa-arrow-left"></i>
                                Kembali</Link>
                            </div>
                            <div class="col-md-2 col-12 mb-2">
                                <Link :href="`/admin/events/${event.id}/certificates/import`"
                                    class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                    class="fa fa-plus-circle"></i> Create Grup</Link>
                            </div>

                            <form @submit.prevent="submit" enctype="multipart/form-data">
                                <div class="row py-4">
                                    <div class="col-md-4">
                                        <span class="text-black">Kategori</span>
                                        <div class="form-group mt-1 mb-4">
                                            <select class="form-control" v-model="form.category">
                                                <option disabled selected>Pilih Kategori</option>
                                                <option value="Kombel">Komunitas Belajar - Peserta</option>
                                                <option value="Kombel-Panitia">Komunitas Belajar - Panitia</option>
                                                <option value="Kombel-Narasumber">Komunitas Belajar - Narasumber</option>
                                                <option value="Kombel-Moderator">Komunitas Belajar - Moderator</option>
                                                <option value="Sayembara">Sayembara</option>
                                                <option value="lain">Lainnya</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-black">NIP</span>
                                        <div class="form-group mt-1 mb-4">
                                            <input type="text" class="form-control" placeholder="Masukan NIP" v-model="form.nip"
                                                @input="searchNIP">
                                        </div>
                                        <div v-if="errors.nip" class="alert alert-danger mt-2">
                                            {{ errors.nip }}
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <span class="text-black">Nama</span>
                                        <div class="form-group mt-1 mb-4">
                                            <input type="text" class="form-control" v-model="form.name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-black">Instansi</span>
                                        <div class="form-group mt-1 mb-4">
                                            <input type="text" class="form-control" v-model="form.agency">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="text-black">Tanggal</span>
                                        <div class="form-group mt-1 mb-4">
                                            <input type="date" class="form-control" v-model="form.date">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-black">Templates</span>
                                        <div class="form-group mt-1 mb-4">
                                            <select class="form-select" v-model="form.template">
                                                <option value="" disabled selected>Pilih salah satu opsi</option>
                                                <option v-for="(template, index) in templates" :key="index"
                                                    :value="template.id">{{ template.title }}</option>
                                            </select>
                                        </div>
                                        <div v-if="errors.template" class="alert alert-danger mt-2">
                                            {{ errors.template }}
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
            </div>
        </template>

        <script>
        //import layout
        import LayoutAdmin from '../../../Layouts/Admin.vue';
        import { ref } from 'vue';
        import { Link, Head } from '@inertiajs/vue3';
        import { router } from '@inertiajs/vue3';
        //import tinyMCE
        import Editor from '@tinymce/tinymce-vue';

        export default {
            layout: LayoutAdmin,
            //register component
            components: {
                Head,
                Link,
                Editor
            },

            props: {
                errors: Object,
                event: Object, // Pass the members array as a prop
                members: Array,
                templates: Array
            },
            setup(props) {
                const form = ref({
                    nip: '',
                    name: '',
                    agency: '',
                    category: '',
                    date: '',
                    template: ''
                });

                const triggerImport = () => {
                    // Logic for import functionality
                    alert('Import functionality not implemented yet.');
                };


                const searchNIP = () => {
                    const member = props.members.find(member => member.nip === form.value.nip);
                    if (member) {
                        form.value.name = member.name;
                        form.value.agency = member.agency;
                    } else {
                        form.value.name = '';
                        form.value.agency = '';
                    }
                };

                // const submit = () => {
                //     const formData = new FormData();
                //     formData.append('nip', form.value.nip);
                //     formData.append('name', form.value.name);
                //     formData.append('agency', form.value.agency);
                //     formData.append('body', form.value.body);
                //     formData.append('category', form.value.category);
                //     formData.append('date', form.value.date);
                //     formData.append('template', form.value.template);
                //     router.post(`/admin/events/${props.event.id}/certificates/import`, formData);
                // };

                const submit = () => {
                    const formData = new FormData();
                    formData.append('nip', form.value.nip);
                    formData.append('name', form.value.name);
                    formData.append('agency', form.value.agency);
                    formData.append('category', form.value.category);
                    formData.append('date', form.value.date);
                    formData.append('template', form.value.template);
                    router.post(`/admin/events/${props.event.id}/certificates/store`, formData);
                };

                return {
                    form,
                    triggerImport,
                    searchNIP,
                    submit
                };
            }
        };
        </script>

        <style scoped>
        /* Add any custom styles here */
        </style>
