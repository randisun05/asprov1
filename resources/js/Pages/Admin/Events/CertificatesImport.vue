            <template>

                <Head>
                    <title>Grup Create</title>
                </Head>
                <div class="px-5 shadow padding">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <h3 class="text-center">Import Sertifikat</h3>
                                <div class="col-md-1 col-12 mb-2">
                                    <Link :href="`/admin/events/${event.id}/certificates`"
                                        class="btn btn-md btn-primary border-0 shadow w-100" type="button"><i
                                        class="fa fa-arrow-left"></i>
                                    Kembali</Link>
                                </div>
                                <form @submit.prevent="submit" enctype="multipart/form-data">
                                    <div class="row py-4">
                                        <div class="col-md-2">
                                            <span class="text-black">Kategori</span>
                                            <div class="form-group mt-1 mb-4">
                                                <select class="form-control" v-model="form.category">
                                                    <option disabled selected>Pilih Kategori</option>
                                                    <option value="Kombel">Komunitas Belajar - Peserta</option>
                                                    <option value="Kombel-Panitia">Komunitas Belajar - Panitia</option>
                                                    <option value="Kombel-Narasumber">Komunitas Belajar - Narasumber</option>
                                                    <option value="Kombel-Moderator">Komunitas Belajar - Moderator</option>
                                                    <option value="sayembara">Sayembara</option>
                                                    <option value="lain">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <span class="text-black">Tanggal</span>
                                            <div class="form-group mt-1 mb-4">
                                                <input type="date" class="form-control" v-model="form.date">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
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

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                            <form @submit.prevent="handleSearch">
                                                <div class="input-group col-md-6">
                                                    <input type="text" class="form-control border-0 shadow" v-model="search" placeholder="masukkan kata kunci dan enter...">
                                                    <span class="input-group-text border-0 shadow">
                                                        <i class="fa fa-search"></i>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless" style="width: 50%;">
                                                        <tbody>
                                                            <tr>
                                                                <th class="border-0 rounded-start" style="width:5%">
                                                                    <input type="checkbox" v-model="form.allSelected"
                                                                        @change="selectAll" />
                                                                </th>
                                                                <th class="border-0 rounded-start">
                                                                    Check All
                                                                </th>
                                                            </tr>
                                                            <tr v-for="(user, index) in users.data" :key="index">
                                                                <td>
                                                                    <input type="checkbox" v-model="form.user_id" :id="user.id"
                                                                        :value="user.member_id" :checked="form.allSelected" />
                                                                </td>
                                                                <td class="text-left">
                                                                    <label class="form-check-label" :for="'user-' + user.id">
                                                                        {{ user.member.name }}
                                                                    </label>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                        </div>
                                        <Pagination :links="users.links" align="end" />
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
              //import component pagination
            import Pagination from '../../../Components/Pagination.vue';

            export default {
                layout: LayoutAdmin,
                //register component
                components: {
                    Head,
                    Link,
                    Editor,
                    Pagination
                },
                props: {
                    errors: Object,
                    event: Object, // Pass the members array as a prop
                    members: Array,
                    templates: Array,
                    users: Array,
                },
                setup(props) {


                     //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get(`/admin/events/${props.event.id}/certificates/import`, {
                    //send params "q" with value from state "search"
                    q: search.value,
                });
            }


                    const form = ref({
                        category: '',
                        date: '',
                        template: '',
                        user_id: [],
                        allSelected: false
                    });

                    const selectAll = () => {
                        form.value.user_id = form.value.allSelected ? props.users.data.map(user => user.member_id) : [];
                        };

                    const submit = () => {
                        const formData = new FormData();
                        formData.append('category', form.value.category);
                        formData.append('date', form.value.date);
                        formData.append('template', form.value.template);
                        formData.append('user_id', form.value.user_id);
                        router.post(`/admin/events/${props.event.id}/certificates/import`, formData);
                    };

                    return {
                        form,
                        submit,
                        selectAll,
                        handleSearch,
                        search
                    };
                }
            };
            </script>

            <style scoped>
            /* Add any custom styles here */
            </style>
