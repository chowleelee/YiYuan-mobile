<template>
  <div class="allspeech">
    <div class="container">
      <ul>
        <li class="speechlist" @click="openspeech(item.id)" v-for="(item,key) in speech_list">
          <div class="listflex">
            <div class="fleximg">
              <img :src="$sourceUrl+'/img/'+item.special_img" height="666" width="666">
            </div>
            <div class="flextitle">
              <p class="titleone">{{item.title}}</p>
              <p class="titletwo">{{item.real_name}} 主讲</p>
              <p class="titlethree">共{{smalltalk_length[key]}}讲，{{join_num[key]}}次参加</p>
            </div>
          </div>
        </li>
      </ul>
  </div>
  </div>
</template>

<script type="es6">
import {selfAdaption} from '../assets/js/common.js'
import {mapMutations} from 'vuex'
import { InfiniteScroll } from 'mint-ui';

export default {
  data () {
    return {
      tishicon:true,
      showdown:true,
      speech_list:[],
      join_num:[],
      smalltalk_length:[]
    }
  },
  components:{

  },
  created(){
    this.init()
  },
  methods:{
    init:function(){
        this.$http
        .get('Special')
        .then(rtnData=>{
          // console.log(rtnData)
          this.speech_list = rtnData.data
          // console.log(this.speech_list)
          for(var i=0;i< this.speech_list.length;i++){
            this.$http
              .get('api/Smalltalk/specialwen',{params:{smalltalk_special_id:this.speech_list[i].id}})
              .then(rtnData=>{
                // console.log(rtnData.data)
                for(var i=0;i<this.speech_list.length;i++){

                  if(this.speech_list[i].id == rtnData.data[0].special_id){
                    var index = i;
                    break;
                  }
                }
                var join_num =0;
                for(var i=0;i<rtnData.data.length;i++){
                  join_num += rtnData.data[i].join_num;
                }
                this.$set(this.join_num,index,join_num)
                this.$set(this.smalltalk_length,index,rtnData.data.length)
                
              })
          }
          
        })
    },
    openspeech:function(n){
      this.$router.push('/stalkcon/'+n);
    },
  } 

  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >
@import '../assets/css/allspeech.css';
 
</style>