<template>
    <v-layout>
        <v-flex xs12 sm4 offset-sm4>
            <v-card id="login" style="padding:0">
                <v-card-title primary-title :class="colorSelected" class="white--text">
					Expense Manager
                </v-card-title>
                <v-card-actions style="padding:20px">
                    <v-form v-on:submit.prevent="login">
                        <v-layout row wrap>
                            <v-flex xs12 sm12 md12>
                                <v-text-field
                                    label= "Email"
                                    v-model="model.email"
                                    solo
                                    prepend-icon="fa-user"
                                    active-class="danger--text"
                                    :rules="[formRules.email, formRules.required]"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                              <v-text-field
                                label="Password"
                                type = "password"
                                name = "password"
                                v-model="model.password"
                                solo
                                prepend-icon="fa-key"
                                :rules="[formRules.required]"
                              ></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm12 md12>
                                <v-btn type="submit" large style="border-radius:10px;width:100%;color:#fff" :class="colorSelected">Login</v-btn>
                            </v-flex>
                        </v-layout>
                    </v-form>
                </v-card-actions>
            </v-card>
        </v-flex>
    </v-layout>
</template>
<script>
    import VueCookies from 'vue-cookies';
    import axios from 'axios';
    export default {
        created : function(){

        },
        data: function(){
            return {
                message : '',
                model : {
                    email: '',
                    password: '',
                },
                colorSelected: "green darken-4",
            }
        },
        methods : {
            login : function(){
                console.log(this.model.email, this.model.password);
                let
                _this = this,
                axiosInstance = axios.create({
                    method: 'post',
                    headers: { }
                }),
                formData = new FormData();
                formData.append('email',_this.model.email);
                formData.append('password', _this.model.password);
                axiosInstance
                .post(_this.apiUrl + '/users/login', formData)
                .then(function(res){
                    if(res.data.status!=false){
                        VueCookies.set(_this.cookieKey, res.data);
                        _this.$router.push('/dashboard');
                    }
                });
            }
        }
    }
</script>
<style>
    #login{
        border-color: transparent;
        border-radius:10px;
        padding:30px;
        width:500px;
        margin: auto;
        top:30%; left: 0; right: 0;
        position:absolute;
        height:auto;
        box-shadow: 0px 0px 15px #888888;
    } .title{
        border-bottom: 3px solid #fdac2c
    } .logoname{
        height:25%;
        width:25%;
    } .logoimage{
        z-index:0;
        position:absolute;
        height:130px;
        width:25%;
        top:-80px;
        left:20px;
    } .change-color {
        transition: all 2s ease;
    } 
</style>
