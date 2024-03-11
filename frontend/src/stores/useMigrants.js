import { defineStore } from 'pinia'
import Migrants from '@/api/migrants'

export const useMigrantsStores = defineStore('migrants', {
    state: () => ({
        migrants: {},
        communityCouncils: {},
        loading: false,
        last_page: 1,
        migrantsTotalCount: 6,
    }),
    getters:{
        getMigrants(){
            return this.migrants
        }
    },
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        fetchMigrants(params) {
            this.setLoading(true)
            
            return Migrants.get(params)
                .then((response) => {
                    this.migrants = response.data.data.migrants.data
                    this.last_page = response.data.data.migrants.last_page
                    this.migrantsTotalCount = response.data.data.migrantsTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        addMigrant(data) {
            this.setLoading(true)

            return Migrants.create(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        showMigrant(id) {
            this.setLoading(true)

            return Migrants.show(id)
                .then((response) => {
                    if(response.data.success)
                        return Promise.resolve(response.data.data.migrant)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        updateMigrant(data) {
            this.setLoading(true)
            
            return Migrants.update(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
         
        },
        deleteMigrant(id) {
            this.setLoading(true)

            return Migrants.delete(id)
                .then((response) => {
                    let index = this.migrants.findIndex((item) => item.id === id)
                    this.migrants.splice(index, 1)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })  
        }
    }
})
