<script setup>

import { useAuthStores } from '@/stores/auth'
import Loader from '@/components/common/Loader.vue'
import check_circle from '@assets/icons/check-circle.svg';
import error_circle from '@assets/icons/error-circle.svg';
import router from '@/router'

const route = useRoute()
const authStores = useAuthStores()

const message = ref('')
const isError = ref(false)
const show = ref(false)
const isLoading = ref(true)

watchEffect(fetchData)

async function fetchData() {

    isLoading.value = true

    authStores.findToken(route.query.token)
        .then(response => {

            message.value = response.message
            show.value = true

            setTimeout(() => { 
                completed(response.data.token)
            }, 3000)

        }).catch(err => {
            isError.value = true
            show.value = true
            isLoading.value = false

            if(err.response.data.feedback === 'not_found' || err.response.data.feedback === 'error_token' )
                message.value = err.response.data.message
            else
                message.value = err.response.data.exception

            console.error(err.response)
        })
}

const completed = (token) => {

    authStores.completed({token: token})
        .then(response => {

            isLoading.value = false
            message.value = response.message

            setTimeout(() => { 
                message.value = ''
                isError.value = false
                router.push({ name: 'login' })
            }, 3000)
            

        }).catch(err => {
            isError.value = true
            isLoading.value = false
            if(err.response.data.feedback === 'not_found') {
                message.value = err.response.data.message
            } else {
                message.value = err.response.data.exception
            }

            console.error(err.response)
        })
}

</script>
<template>
    <VContainer class="mt-1 mt-md-10 confirm">
        <Loader :isLoading="isLoading"/>
        <VCard 
            v-if="show"
            class="mt-10 px-10 py-14 pb-2 pb-md-4 no-shadown card-register d-block text-center mx-auto">
            <VImg width="100" :src="isError ? error_circle : check_circle" class="mx-auto"/>
            <VCardText class="text-message mt-10 mb-5">
                {{ message }}
            </VCardText>
        </VCard>
    </VContainer>
</template>

<style scoped>

    .text-message {
        color:  #FF0090;
        text-align: center;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: 30px; 
        padding: 0 80px !important;
    }

    .confirm {
        height: 600px;
    }

    .card-register {
        padding: 20px;
        border-radius: 32px !important;
        width: 500px; 
    }

    @media only screen and (max-width: 767px) {
        .card-register {
            width: auto;
            padding: 40px 20px !important;
        }

        .text-message {
            padding: 0 30px !important;
            font-size: 18px;
        }

    }
</style>