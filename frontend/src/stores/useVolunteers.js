import { defineStore } from 'pinia'
import Volunteers from '@/api/volunteers'

export const useVolunteersStores = defineStore('volunteers', {
    state: () => ({
        //
    }),
    actions: {
        setLoading(payload){
            this.loading = payload
        },
        register(data) {
            this.setLoading(true)

            return Volunteers.register(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        }
    }
})
