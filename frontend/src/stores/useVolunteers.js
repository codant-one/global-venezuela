import { defineStore } from 'pinia'
import Volunteers from '@/api/volunteers'

export const useVolunteersStores = defineStore('volunteers', {
    state: () => ({
        volunteers: {},
        loading: false,
        last_page: 1,
        volunteersTotalCount: 6,
    }),
    getters:{
        getVolunteers(){
            return this.volunteers
        }
    },
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
            
        },
        fetchVolunteers(params) {
            this.setLoading(true)
            
            return Volunteers.get(params)
                .then((response) => {
                    this.volunteers = response.data.data.volunteers.data
                    this.last_page = response.data.data.volunteers.last_page
                    this.volunteersTotalCount = response.data.data.volunteersTotalCount
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        addVolunteer(data) {
            this.setLoading(true)

            return Volunteers.create(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        showVolunteer(id) {
            this.setLoading(true)

            return Volunteers.show(id)
                .then((response) => {
                    if(response.data.success)
                        return Promise.resolve(response.data.data.volunteer)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
            
        },
        updateVolunteer(data) {
            this.setLoading(true)
            
            return Volunteers.update(data)
                .then((response) => {
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })
         
        },
        deleteVolunteer(id) {
            this.setLoading(true)

            return Volunteers.delete(id)
                .then((response) => {
                    let index = this.volunteers.findIndex((item) => item.id === id)
                    this.volunteers.splice(index, 1)
                    return Promise.resolve(response)
                })
                .catch(error => Promise.reject(error))
                .finally(() => {
                    this.setLoading(false)
                })  
        }
    }
})
