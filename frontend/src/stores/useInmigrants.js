import { defineStore } from 'pinia'
import Inmigrants from '@/api/inmigrants'

export const useInmigrantsStores = defineStore('inmigrants', {
    state: () => ({
        inmigrants: {},
        communityCouncils: {},
        loading: false,
        last_page: 1,
        inmigrantsTotalCount: 6,
    }),
    getters:{
        getInmigrants(){
            return this.inmigrants
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        fetchInmigrants(params) {
            this.setLoading(true)
            
            return Inmigrants.get(params)
                .then((response) => {
                    this.inmigrants = response.data.data.inmigrants.data
                    this.last_page = response.data.data.inmigrants.last_page
                    this.inmigrantsTotalCount = response.data.data.inmigrantsTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        addInmigrant(data) {
            this.setLoading(true)

            return Inmigrants.create(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        showInmigrant(id) {
            this.setLoading(true)

            return Inmigrants.show(id)
                .then((response) => {
                    if(response.data.success)
                        return Promise.resolve(response.data.data.inmigrant)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        updateInmigrant(data) {
            this.setLoading(true)
            
            return Inmigrants.update(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
         
        },
        deleteInmigrant(id) {
            this.setLoading(true)

            return Inmigrants.delete(id)
                .then((response) => {
                    let index = this.inmigrants.findIndex((item) => item.id === id)
                    this.inmigrants.splice(index, 1)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })  
        }
    }
})
