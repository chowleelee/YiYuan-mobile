<template>
  <div class="showresult">
    <div class="search">
      <div class="container">
        <input type="text" class="search-text" v-model="keyword" placeholder="搜索感兴趣的人和问题" />  
        <mt-button type="danger" class="search-btn" @click='showresult()'>搜索</mt-button>
      </div>
    </div>
    <div class="">
      <mt-navbar v-model="selected" class="search-nav container">
        <mt-tab-item :id="'tab_'+key" v-for="(item,key) in nav_list">{{item.name}}</mt-tab-item>
      </mt-navbar>
    </div>
    <div class="result-wrap">
      <mt-tab-container v-model="selected" infinite-scroll-distance="10">
        <mt-tab-container-item :id="'tab_'+key" v-for="(item,key) in nav_list">
          <p class="wrap-title container" v-if="item.flag!=1 && item.show">{{item.name}}</p>
          <div class="composed" v-if="item.flag==1">
            <div class="composed-wrap" v-if="nav_list[2].show">
              <div class="composed-item container">
                <p class="wrap-title">答主</p>
                <mt-cell v-for="(it,id) in toshow['vip_list']">
                  <div class="search-result" @click="toTutor(it.id)">
                    <div class="img">
                      <img :src="$sourceUrl+'/img/'+it.head_img" alt="">
                    </div>
                    <div class="info">
                      <div class="tutor">
                        <span>{{it.real_name}}</span>
                        <span> | </span>
                        <span>{{it.identity}}</span>
                      </div>              
                      <div class="join_number">
                        <span>{{it.answer_num}}个回答 </span>
                        <span> {{it.listen_num}}人收听</span>
                      </div>
                    </div>
                  </div>
                </mt-cell>
                <p class="more" @click="moreTutor()">
                  <span></span>
                  <span>查看更多人物</span>
                  <span></span>
                </p>
              </div>
            </div>
            <div class="composed-wrap" v-if="nav_list[3].show">
              <div class="composed-item container">
                <p class="wrap-title">问题</p>
                <mt-cell v-for="(it,id) in toshow['ask_list']">
                  <div class="search-result" @click="toQues(it.id)">
                    <div class="ques">
                      <div class="title">
                        <span>{{it.ask_content}}</span>
                      </div>              
                      <div class="tutor">
                        <span>答主：{{it.real_name}}</span>
                        <span> | </span>
                        <span>{{it.identity}}</span>
                      </div> 
                    </div>
                  </div>
                </mt-cell>
                <p class="more" @click="moreQues()">
                  <span></span>
                  <span>查看更多问题</span>
                  <span></span>
                </p>
              </div>
            </div>
            <div class="composed-wrap" v-if="nav_list[1].show">
              <div class="composed-item container">
                <p class="wrap-title">小讲</p>
                <mt-cell v-for="(it,id) in toshow['smalltalk_list']">
                  <div class="search-result" @click="toTalk(it.id)">
                    <div class="img">
                      <img :src="$sourceUrl+'/img/'+it.head_img" alt="">
                    </div>
                    <div class="info">
                      <p class="title">{{it.title}}</p>
                      <div class="tutor">
                        <span>{{it.real_name}}</span>
                        <span> | </span>
                        <span>{{it.identity}}</span>
                      </div>              
                      <div class="join_number">
                        {{it.join_num}}人参加
                      </div>
                    </div>
                  </div>
                </mt-cell>
                <p class="more" @click="moreTalk()">
                  <span></span>
                  <span>查看更多小讲</span>
                  <span></span>
                </p>
              </div>
            </div>
          </div>
          <mt-cell v-for="(it,id) in toshow['smalltalk_list']" v-if="item.flag==2 && item.show">
            <div class="search-result container" @click="toTalk(it.id)">
              <div class="img">
                <img :src="$sourceUrl+'/img/'+it.head_img" alt="">
              </div>
              <div class="info">
                <p class="title">{{it.title}}</p>
                <div class="tutor">
                  <span>{{it.real_name}}</span>
                  <span> | </span>
                  <span>{{it.identity}}</span>
                </div>              
                <div class="join_number">
                  {{it.join_num}}人参加
                </div>
              </div>
            </div>
          </mt-cell>
          <mt-cell v-for="(it,id) in toshow['vip_list']" v-if="item.flag==3 && item.show">
            <div class="search-result container" @click="toTutor(it.id)">
              <div class="img">
                <img :src="$sourceUrl+'/img/'+it.head_img" alt="">
              </div>
              <div class="info">
                <div class="tutor">
                  <span>{{it.real_name}}</span>
                  <span> | </span>
                  <span>{{it.identity}}</span>
                </div>              
                <div class="join_number">
                  <span>{{it.answer_num}}个回答 </span>
                  <span>{{it.listen_num}}人收听</span>
                </div>
              </div>
            </div>
          </mt-cell>
          <mt-cell v-for="(it,id) in toshow['ask_list']" v-if="item.flag==4 && item.show">
            <div class="search-result container" @click="toQues(it.id)">
              <div class="ques">
                <div class="title">
                  <span>{{it.ask_content}}</span>
                </div>              
                <div class="tutor">
                  <span>答主：{{it.real_name}}</span>
                  <span> | </span>
                  <span>{{it.identity}}</span>
                </div> 
              </div>
            </div>
          </mt-cell>
          <div class="none-info" v-if="!item.show">
            <img src="../assets/img/answer4.jpg" alt="">
            <p>没有相关搜索结果</p>
          </div>
        </mt-tab-container-item>
      </mt-tab-container>
    </div>
    
      <!-- <div v-if="recommend">
        <p>推荐答案</p>
        <p>
          {{toshow['recommend_list'][0].title}}
          <br>
          {{toshow['recommend_list'][0].real_name}}  
          {{toshow['recommend_list'][0].identity}}
          <br>
          {{toshow['recommend_list'][0].join_num}}人参加
        </p>
      </div> -->
  </div>
  
</template>

<script type="es6">
 import {mapState,mapMutations} from 'vuex' 

export default {
  data () {
    return {
      toshow:[],
      selected : 'tab_0',
      nav_list:[
        {name:'综合',flag:1,show:true},
        {name:'小讲',flag:2,show:false},
        {name:'人物',flag:3,show:false},
        {name:'问题',flag:4,show:false}
      ],
      keyword:''
    }
  },
  created(){
    this.init();
  },
  computed:{
    ...mapState(['searchContent'])
  },  
  methods:{
    ...mapMutations(['setSearchContent']),
    init:function(){
      this.keyword = this.keyword? this.keyword:this.searchContent;
      this.$http
      .get('serch',{params:{key_word:this.keyword}})
      .then(rd=>{
        this.toshow = rd.data
        if (this.toshow['recommend_list'].length) {
        }
        if (this.toshow['vip_list'].length) {
          this.nav_list[2].show = true;
        }else{
          this.nav_list[2].show = false;
        }
        if (this.toshow['ask_list'].length) {
          this.nav_list[3].show = true;
        }else{
          this.nav_list[3].show = false;
        }
        if (this.toshow['smalltalk_list'].length) {
          this.nav_list[1].show = true;
        }else{
          this.nav_list[1].show = false;
        }
        if(this.toshow['vip_list'].length == 0 && this.toshow['ask_list'].length == 0 && this.toshow['smalltalk_list'].length == 0){
          this.nav_list[0].show = false;
        }else{
          this.nav_list[0].show = true;
        }
      })
    },
    moreTutor:function(){
      this.selected = 'tab_2';
    },
    moreQues:function(){
      this.selected = 'tab_3';
    },
    moreTalk:function(){
      this.selected = 'tab_1';
    },
    toTutor:function(id){
      this.$router.push('/tutorDetails/'+id);
    },
    toTalk:function(id){
      this.$router.push('/stalkteacher/'+id);
    },
    toQues:function(id){
      this.$router.push('/problemDetails/'+id);
    },
    showresult:function(){
      this.init();
      var history = JSON.parse(localStorage.getItem("history"))
      for(let i = 0; i < history.length; i++){
        if(this.keyword == history[i]){
          history.splice(i,1);
        }
      }
      history.unshift(this.keyword);
      localStorage.setItem('history',JSON.stringify(history));
    }
  }
    	
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

@import '../assets/css/showresult.css';
</style>