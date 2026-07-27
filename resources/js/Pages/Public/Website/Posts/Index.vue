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
                            <li class="breadcrumb-item active" aria-current="page">{{ title }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-3">
                </div>

                <div class="col-sm-6 mb-4">
                    <div class="text-center">
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
    </section>
    <!--page Header ends-->


    <!-- Our Blogs -->
    <section id="our-blog">
        <div class="container padding">
            <div class="row mb-4">
                <div v-for="(post, index) in posts.data" :key="index" class="col-md-4 py-3">
                    <div class="news_item shadow text-center">

                        <img class="image" style="width: 100%;" v-if="post.image" :src="getImageUrl(post.image)" alt="Gambar" />

                        <div class="news_desc">
                            <h3 class="text-capitalize font-light darkcolor" style="font-weight: bold;"><a href="#.">{{
                        post.title }}</a></h3>
                            <ul class="meta-tags top20 bottom20">
                                <li><a href="#."><i class="fa fa-calendar"></i>{{ post.publish_at }}</a></li>
                                <li><a href="#."> <i class="fa fa-user-o"></i>{{ post.member.name }}</a></li>
                            </ul>
                            <p class="bottom35">{{ post.excerpt }}</p>

                            <Link :href="post.category_id === 3 ? `/sdma-menulis/${post.slug}` : `/berita/${post.slug}`" title="join" class="button btnprimary">
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
                            <!-- <label for="view">
                                <i class="fa fa-eye" :class="{ active: post.react.view > 0 }" alt="View" :title="'Dilihat ' + post.react.view + ' kali'"></i>
                               dilihat {{ post.react.view }} kali
                            </label> -->
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
    ref, watch,
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
        title: String,
        errors: Object,
        posts: Object,
    },



    //inisialisasi composition API
    setup(props) {

        //define state search
        const search = ref('' || (new URL(document.location)).searchParams.get('q'));

        //define method search
        const handleSearch = () => {
            const currentPath = window.location.pathname;
            const searchUrl = currentPath.includes('/artikel') ? '/artikel' : '/berita';

            router.get(searchUrl, {
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
<style>
.post {
  display: flex;
  align-items: center;
}

label {
  margin-right: 10px;
}

/* img {
  width: 20px;
  height: 20px;
  margin-right: 5px;
} */

.active {
  filter: brightness(1.5); /* Highlight the active image */
}
</style>
