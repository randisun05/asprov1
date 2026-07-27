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
      </div>
   </div>
</section>
<!--page Header ends-->


<section id="our-testimonial" class="padding_m">

   <div class="container">
      <div class="row">
         <div class="col-md-7 col-sm-7 wow fadeInLeft" data-wow-delay="300ms">
            <div class="heading-title text-md-start text-center padding_bottom">
               <span>Visi</span>
               <h5 class="fontregular bottom20 darkcolor">menjadi organisasi kapabel, profesional, dan produktif dalam mendukung penyelenggaraan manajemen SDM Aparatur.</h5>
               <span>Misi</span>
            <div style="text-align: justify;">
               <h5 class="fontregular bottom20 darkcolor">
                 <div v-html="data.body"></div>
                  <!-- <table>
                        <tr>
                              <td width="5%">1.</td>
                              <td>Meningkatkan kontribusi professional Jabatan Fungsional SDMA dalam penyelenggaraan manajemen Aparatur Sipil Negara.</td>
                        </tr>
                        <tr>
                              <td>2.</td>
                              <td>Membangun profesionalisme Pejabat Fungsional SDMA yang dinamis dan berkelanjutan.</td>
                        </tr>
                        <tr>
                              <td>3.</td>
                              <td>Membangun dan mengembangkan sinergi antarpejabat Fungsional SDMA.</td>
                        </tr>
                        <tr>
                              <td>4.</td>
                              <td>Meningkatkan kapabilitas pejabat fungsional SDMA dengan menggunakan berbagai media dan model pembelajaran.</td>
                        </tr>
                        <tr>
                              <td>5.</td>
                              <td>Membangun dan mengembangkan kemitraan dengan instansi pemerintah dan swasta secara professional.</td>
                        </tr>
               </table> -->
               </h5>
            </div>
         </div>
         </div>
         <div class="col-md-5 col-sm-5 wow fadeInRight" data-wow-delay="350ms">
            <div class="image hover-effect">
                <div class="image center">
                    <img v-if="data.image" :src="getImageUrl(data.image)" alt="Gambar" />
                </div>
                 <!-- <img alt="SEO" src="/assets/images/visi.jpg"> -->
                </div>
         </div>
      </div>
   </div>
</section>

<!--Testimonials Ends-->


</template>

<script>
    //import layout
    import LayoutWebsite from '../../../../Layouts/Website.vue';


    //import reactive
    import {
        reactive
    } from 'vue'

    //import inertia adapter
    import { router } from '@inertiajs/vue3';

     //import Head from Inertia
     import {
      Head
    } from '@inertiajs/vue3';


    export default {

        //layout
        layout: LayoutWebsite,

        //register component
        components: {
            Head,

        },

        //props
        props: {
            title:Object,
            data: Object
        },

        //inisialisasi composition API
        setup() {


            // Method to get the URL of the document
            const getImageUrl = (imageName) => {
                return `/storage/${imageName}`;
            }


            //return
            return {
                getImageUrl,
            }
            }

    }

</script>

<style>
.image.center {
    display: flex;
    justify-content: center; /* horizontal */
    align-items: center;     /* vertical */
    height: 100%;            /* pastikan ada tinggi */
    min-height: 200px;       /* biar nggak collapse kalau gak ada konten */
    text-align: center;
}
</style>

