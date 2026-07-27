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
         <div class="col-md-12 col-sm-12 text-center">
            <div class="heading-title bottom30">
               <div class="image hover-effect bottom30">
                <div class="mt-3">
                    <img v-if="data.image" :src="getImageUrl(data.image)" alt="Gambar" />
                    <!-- <img alt="SEO" src="/assets/images/sejarah.jpg"> -->
                </div>
               </div>
         </div>
                <div style="text-align: justify;">
                    <!-- <p class="h6 bottom20 darkcolor mb-4">Hadirnya Asosiasi Profesi Jabatan Fungsional Sumber Daya Manusia Aparatur pada tanggal 23 Oktober 2023 tidak terlepas dari semangat tranformasi dari  Jabatan  Fungsional Analis Kepegawaian  menjadi  Analis  Sumber  Daya  Manusia  Aparatur  dan  Pranata  SDM  Aparatur .  Jabatan ini bernaung dalam Asosiasi Profesi bernama Ikatan Profesi Analis Kepegawaian Republik Indonesia atau  IPAKRI yang  lahir  pada  tanggal 15 Mei 2015. IPAKRI merupakan bentuk pemutakhiran dari Organisasi  Profesi  Analis  Kepegawaian  sebelumnya  yaitu Ikatan Analis Kepegawaian Indonesia atau IAKI, yang lahir pada tanggal 13 September 2012. </p>

                    <p class="h6 bottom20 darkcolor mb-4">IPAKRI di bawah kepemimpinan H. Jono, S.Sos., M.M.,  telah  menorehkan banyak  kiprah  dalam  membangun profesionalisme para pejabat Analis Kepegawaian serta memberikan pengalaman  berorganisasi  bagi  para pejabat  fungsional  Analis Kepegawaian. Bersama tujuh Kepengurusan Cabang, IPAKRI berhasil memberikan landasan yang kokoh bagi terbangunnya Aspro SDMA yang sebagaian besar memiliki nafas dan semangat serta filosofi yang sama sebagai Insan Kepegawaian.
                    </p>
                    <p class="h6 bottom20 darkcolor mb-4">Aspro SDMA yang merupakan tranformasi dari IPAKRI merupakan langkah baru dalam upaya membangun organisasi yang dinamis dan sejalan dengan tuntutan jaman.  Aspro SDMA merupakan langkah awal para Analis SDM Aparatur dan Pranata SDM Aparatur yang senantiasa terbuka pada goresan kiprah dan karya para anggotanya sebagai wujud sejarah organisasi ini di masa yang akan datang.
                    </p> -->
                    <div v-html="data.body"></div>
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
