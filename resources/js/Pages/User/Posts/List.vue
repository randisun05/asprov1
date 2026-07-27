<template>

    <Head>
        <title>Daftar Berita</title>
    </Head>
    <div class="px-5 shadow padding">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-12 mb-2 py-3">
                        <form @submit.prevent="handleSearch">
                            <div class="input-group">
                                <input type="text" class="form-control border-0 shadow" v-model="search"
                                    placeholder="masukkan kata kunci dan enter...">
                                <span class="input-group-text border-0 shadow">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    <!-- Our Blogs -->
    <section id="our-blog">
        <div class="padding_m card">
            <div class="row text-center">
                    <h3>Daftar Berita</h3>
                </div>
            <div v-if="!posts.data.length" class="text-center py-5 text-muted">
                <i class="fa fa-newspaper-o fa-3x mb-3 d-block" aria-hidden="true"></i>
                Belum ada berita yang tersedia saat ini.
            </div>
            <div class="row mb-4">
                <div v-for="(post, index) in posts.data" :key="index" class="col-md-4 py-3">
                    <div class="news_item shadow text-center">
                        <img class="image" style="width: 100%; display: flex; justify-content: center; align-items: center;" v-if="post.image" :src="getImageUrl(post.image)" alt="Gambar" />
                        <div class="news_desc">
                            <h4 class="text-capitalize font-light darkcolor" style="font-weight: bold;"><a href="#.">{{
            post.title }}</a></h4>
                            <ul class="meta-tags top20 bottom20">
                                <li><a href="#."><i class="fa fa-calendar"></i>{{ post.publish_at }}</a></li>
                                <li><a href="#."> <i class="fa fa-user-o"></i>{{ post.member.name }}</a></li>
                            </ul>
                            <p class="bottom35">{{ post.excerpt }}</p>
                            <Link :href="`/user/posts/list/${post.slug}`" title="join" class="button btnprimary" type="button">
                            View</Link>
                            <div>
                                    <!-- <label for="love">
                                    <img :src="getImageUrl('love.png')" :class="{ active: post.react.love > 0 }" alt="Love">
                                    {{ post.react.love }}
                                </label>
                                <label for="like">
                                    <img :src="getImageUrl('thumbs_up.png')" :class="{ active: post.react.like > 0 }" alt="Like">
                                    {{ post.react.like }}
                                </label> -->
                            <label for="view">
                                <i class="fa fa-eye" :class="{ active: post.react.view > 0 }" alt="View" :title="'Dilihat ' + post.react.view + ' kali'"></i>
                               dilihat {{ post.react.view }} kali
                            </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center"> <!-- Center align pagination -->
                    <Pagination :links="posts.links" align="center" />
                </div>
            </div>
        </div>
    </section>
    </div>
    <!--Our Blogs Ends-->

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
} from '@inertiajs/inertia-vue3';

//import ref from vue
import {
    ref, watch
} from 'vue';

//import inertia adapter
import { Inertia } from '@inertiajs/inertia';

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
        title: Object,
        errors: Object,
        posts: Object,
    },

    //inisialisasi composition API
    setup(props) {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            Inertia.get('/user/posts/list', {

                //send params "q" with value from state "search"
                q: search.value,
            });
        }

        // Method to get the URL of the document
        const getImageUrl = (imageName) => {
            return `/storage/${imageName}`;
        }

         // Method to count reactions
         const countReactions = (posts) => {
            posts.data.forEach(post => {
                // Ensure post.react is an array before filtering
                if (Array.isArray(post.react)) {
                    // Filter and count reactions based on react_id
                    const loveReactions = post.react.filter(reaction => reaction.react_id === "1").length;
                    const likeReactions = post.react.filter(reaction => reaction.react_id === "2").length;
                    const viewReactions = post.react.filter(reaction => reaction.react_id === "3").length;

                    // Assign the counts to post.react as an object
                    post.react = {
                        love: loveReactions,
                        like: likeReactions,
                        view: viewReactions,
                    };
                } else {
                    // If react is not an array, set default counts
                    post.react = {
                        love: 0,
                        like: 0,
                        view: 0,
                    };
                }
            });
        }

        // Watch for changes in posts and count reactions whenever it updates
        watch(() => props.posts, (newPosts) => {
            countReactions(newPosts); // Count reactions whenever posts update
        }, { immediate: true }); // Run the function immediately on first render

        //return
        return {
            search,
            handleSearch,
            getImageUrl,
            countReactions


        }
    }

}

</script>
