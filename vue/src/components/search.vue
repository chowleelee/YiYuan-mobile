 <template >
  <div class="mysearch">
    <p class="search" @click="search()" v-if="!isSearch"><i class="search-icon" ></i>搜索答主和问题</p>
    <div class="searching" v-if="isSearch">
      <div class="container">
        <div class="page-search" >  
          <span class="cancel" @click="search()">关闭</span>
          <input type="text" class="search-text"  v-model="keyWord" placeholder="搜索感兴趣的人和问题" />  
          <mt-button type="danger" class="search-btn" @click='showres()'>搜索</mt-button>
        </div> 
        <div class="hot">
          <p class="hot-title">热门搜索</p>
          <ul>
            <li v-for="(item,index) in 10" @click='showres(item)'>{{index+1}}</li>
          </ul>
        </div>
        <div v-if='flag' class="history">
          <div class="history-title" v-if="history_length!=0">
            <span>历史搜索</span>
            <span @click='delAll()'></span>
          </div>
          <ul>
            <li v-for='(item,index) in history_list'>
              <span @click='showres(item)'>{{item}}</span> 
              <span @click='delItem(index)'></span>
            </li>
          </ul>  
        </div>   
      </div>
    </div>
    <div class="tips">
      <mt-popup
        v-model="show"
        popup-transition="popup-fade">请输入搜索内容
      </mt-popup>
    </div>
  </div>
 </template>

<script type="es6">
import {mapMutations} from 'vuex'

export default {
  data(){
    return {
      isSearch:false,
      history_list:[],
      keyWord:"",
      flag:false,
      show:false
    }
  },
  watch:{
    keyWord: function (new_value) {
      this.setSearchContent(new_value.trim());
    }
  },
  computed:{
    history_length:function(){
      return this.history_list.length;
    }
  },
  created(){
    this.init()
  },
  methods:{
    ...mapMutations(['setSearchContent']),
    init:function() {
      this.history_list = JSON.parse(localStorage.getItem("history")) || [];
      if (this.history_list.length) {
        this.flag=true
      }
    },
    search:function(){
      if(this.isSearch){
        this.isSearch = false;
      }else{
        this.isSearch = true;
      }
      this.$emit('child-say',this.isSearch);
    },
    showres:function(id=''){
      let keyword = !id ? this.keyWord.trim() : id;
      if(keyword == ''){
        this.show = true;
        return;
      }
      for(let i = 0; i < this.history_list.length; i++){
        if(keyword == this.history_list[i]){
          this.history_list.splice(i,1);
        }
      }
      this.history_list.unshift(keyword);
      localStorage.setItem('history',JSON.stringify(this.history_list));
      this.setSearchContent(keyword); 
      this.$router.push('/showresult');
    },
    /*recommond:function(value) {
      var tosearch =value;
      this.history_list.push(tosearch)
      localStorage.setItem('history',JSON.stringify(this.history_list))     
      this.setSearchContent(tosearch); 
      this.$router.push('/showresult');    
    },*/
    delItem:function(which) { 
      this.history_list.splice(which,1);
      localStorage.setItem('history',JSON.stringify(this.history_list));
    },
    delAll:function() { 
      this.history_list = []; 
      this.flag = false;
      localStorage.removeItem('history');
    }
  }
}
</script>
<style>
@import '../assets/css/search.css';
 
</style>