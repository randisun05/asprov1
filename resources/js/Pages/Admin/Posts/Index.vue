<template>
    <Head>
        <title>Administrator</title>
    </Head>
    <div class="container-fluid padding px-5">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/admin/posts" class="btn btn-md btn-primary border-0 shadow w-100" type="button">
                            <i class="fa fa-plus-circle"></i>  Post
                        </Link>
                    </div>

                    <div class="col-md-1 col-12 mb-2">
                        <Link href="/admin/category/create" class="btn btn-md btn-warning border-0 shadow w-100" type="button">
                            <i class="fa fa-plus-circle"></i>  Kategori
                        </Link>
                    </div>



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
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                            <a class="nav-link" :class="{ active: activeTab === 'sub' }" @click="setActiveTab('sub')">Submission</a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" :class="{ active: activeTab === 'approved' }" @click="setActiveTab('approved')">Publish</a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" :class="{ active: activeTab === 'limited' }" @click="setActiveTab('limited')">Limited</a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link" :class="{ active: activeTab === 'category' }" @click="setActiveTab('category')">Category</a>
                            </li>
                        </ul>

                        <div v-show="activeTab === 'sub'" class="table-responsive" id="sub">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Title</th>
                                        <th class="border-0">Author</th>
                                        <th class="border-0">Kategori</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(post, index) in posts.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (posts.current_page - 1) * posts.per_page }}</td>
                                        <td>{{ post.title }}</td>
                                        <td>{{ post.member_id !== 1 ? post.member.name : 'Administrator' }} </td>
                                        <td>{{ post.category.title }}</td>
                                        <td> <span v-if="post.status === 'approved'" class="badge bg-success" title="disetujui untuk di publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'private'" class="badge bg-warning" title="hanya untuk konsumsi pribadi">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'perlu ada perbaikan'" class="badge bg-warning" title="silakan dicek kembali">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'rejected'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'submission'" class="badge bg-secondary">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'return'" class="badge bg-danger" title="ditolak untuk publish">{{ post.status }}</span>
                                            <span v-else-if="post.status === 'limited'" class="badge bg-secondary">{{ post.status }}</span></td>
                                        <td class="text-center">
                                            <Link :href="`/admin/posts/${post.id}`" title="view" class="btn btn-sm btn-primary border-0 shadow me-2" type="button"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></Link>
                                            <button @click="handleApprove(post.id)" title="setuju untuk publish" class="btn btn-sm btn-success border-0 shadow me-2" type="button"><i class="fa fa-check-circle fa-lg" aria-hidden="true"></i></button>
                                            <Link :href="`/admin/posts/${post.id}/edit`" title="edit" class="btn btn-sm btn-warning border-0 shadow me-2">
                                            <i class="fa fa-pencil fa-lg" aria-hidden="true"></i></Link>
                                            <button @click="handleLimited(post.id)" title="setuju untuk limited publish" class="btn btn-sm btn-success border-0 shadow me-2" type="button"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i></button>
                                            <button @click="handleReject(post.id)" title="tolak" class="btn btn-sm btn-danger border-0 shadow me-2"><i class="fa fa-times-circle fa-lg" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-show="activeTab === 'approved'" class="table-responsive" id="approved">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Title</th>
                                        <th class="border-0">Kategori</th>
                                        <th class="border-0">Author</th>
                                        <th class="border-0">Views</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(publish, index) in publishs.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (publishs.current_page - 1) * publishs.per_page }}</td>
                                        <td>{{ publish.post.title }}</td>
                                        <td>{{ publish.post.category.title }}</td>
                                        <td>{{ publish.post.member.name !== 1 ? publish.post.member.name : 'Administrator' }} </td>
                                        <td>
                                            {{ publish.post.react.view }}
                                        </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/posts/${publish.post.id}`" title="view" class="btn btn-sm btn-primary border-0 shadow me-2" type="button"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></Link>
                                            <button @click="handleCancel(publish.id)" title="tolak" class="btn btn-sm btn-danger border-0 shadow me-2"><i class="fa fa-times-circle fa-lg" aria-hidden="true"></i></button>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-show="activeTab === 'limited'" class="table-responsive" id="limited">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Title</th>
                                        <th class="border-0">Kategori</th>
                                        <th class="border-0">Author</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(limited, index) in limiteds.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (limiteds.current_page - 1) * limiteds.per_page }}</td>
                                        <td>{{ limited.title }}</td>
                                        <td>{{ limited.category.title }}</td>
                                        <td>{{ limited.member.name !== 1 ? limited.member.name : 'Administrator' }} </td>
                                        <td class="text-center">
                                            <Link :href="`/admin/posts/${limited.id}`" title="view" class="btn btn-sm btn-primary border-0 shadow me-2" type="button"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></Link>
                                            <button @click="handleCancelLimited(limited.id)" title="tolak" class="btn btn-sm btn-danger border-0 shadow me-2"><i class="fa fa-times-circle fa-lg" aria-hidden="true"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-show="activeTab === 'category'" class="table-responsive" id="category">
                            <table class="table table-bordered table-centered table-nowrap mb-0 rounded">
                                <thead class="thead-dark">
                                    <tr class="border-0 text-center">
                                        <th class="border-0 rounded-start" style="width:5%">No.</th>
                                        <th class="border-0">Title</th>
                                        <th class="border-0 rounded-end" style="width:12%">Aksi</th>
                                    </tr>
                                </thead>
                                <div class="mt-2"></div>
                                <tbody>
                                    <tr v-for="(category, index) in categories.data" :key="index">
                                        <td class="fw-bold text-center">{{ ++index + (categories.current_page - 1) * categories.per_page }}</td>
                                        <td>{{ category.title }}</td>
                                        <td class="text-center">
                                            <button @click.prevent="destroy(category.id)" class="btn btn-sm btn-danger border-0 me-2"><i class="fa fa-trash" title="hapus"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination v-if="activeTab === 'sub'" :links="posts.links" align="end" />
                        <Pagination v-if="activeTab === 'approved'" :links="publishs.links" align="end" />
                        <Pagination v-if="activeTab === 'limited'" :links="limiteds.links" align="end" />
                        <Pagination v-if="activeTab === 'category'" :links="categories.links" align="end" />
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
        Link
    } from '@inertiajs/vue3';

    //import ref from vue
    import {
        ref, watch,
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
            Pagination
        },

        //props
        props: {
            errors: Object,
            posts: Object,
            publishs: Object,
            categories: Object,
            limiteds: Object,
        },

        data() {
            return {
              activeTab: 'sub', // Set the default active tab

            };
          },

                  methods: {
            setActiveTab(tabName) {
              this.activeTab = tabName;
            },
          },

        //inisialisasi composition API
        setup(props) {

            //define state search
            const search = ref('' || (new URL(document.location)).searchParams.get('q'));

            //define method search
            const handleSearch = () => {
                router.get('/admin/posts', {

                    //send params "q" with value from state "search"
                    q: search.value,
                });
            }

            const handleApprove = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Post akan dipublikasi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Approve it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/posts/${id}/approve`);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Approved!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }

            const handleReturn = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Post akan dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Return it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        router.post(`/admin/posts/${id}/return`);
                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Returned!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }



            const handleReject = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan menolak pengajuan ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Reject it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/posts/${id}/reject`);

                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Rejected!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }

            const handleCancel = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan membatalkan publikasi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Reject it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/posts/${id}/cancel`);

                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Cancelled!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }

            const handleLimited = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Publikasi akan bersifat terbatas!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Aprrove it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/posts/${id}/limited`);

                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Limited!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }

            const handleCancelLimited = (id) => {
            Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan membatalkan publikasi!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Reject it!'
                })
                .then((result) => {
                    if (result.isConfirmed) {

                        router.post(`/admin/posts/${id}/cancelLimited`);

                        Swal.fire({
                            title: 'Success!',
                            text: 'Status Cancelled!.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                    }
                })
            }
             // Method to count reactions
             const countReactions = (publishs) => {
                publishs.data.forEach(publish => {
                    // Ensure publish.post exists and post.react is an array before filtering
                    if (publish.post && Array.isArray(publish.post.react)) {
                        // Filter and count reactions based on react_id
                        const loveReactions = publish.post.react.filter(reaction => reaction.react_id === "1").length;
                        const likeReactions = publish.post.react.filter(reaction => reaction.react_id === "2").length;
                        const viewReactions = publish.post.react.filter(reaction => reaction.react_id === "3").length;

                        // Assign the counts to post.react as an object
                        publish.post.react = {
                            love: loveReactions,
                            like: likeReactions,
                            view: viewReactions,
                        };
                    } else if (publish.post) {
                        // If react is not an array, set default counts
                        publish.post.react = {
                            love: 0,
                            like: 0,
                            view: 0,
                        };
                    }
                });
            };
        // Watch for changes in posts and count reactions whenever it updates
        watch(() => props.publishs, (newPosts) => {
            countReactions(newPosts); // Count reactions whenever posts update
        }, { immediate: true }); // Run the function immediately on first render




            //return
            return {
                search,
                handleSearch,
                handleReject,
                handleApprove,
                handleReturn,
                handleCancel,
                handleLimited,
                countReactions,
                handleCancelLimited


        }
    }
}

</script>

<style>

</style>
