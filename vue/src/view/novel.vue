<template>
  <div class="novel">
    <div class="list-nav">
      <div class="container">
        <a @click="touhome()">取消</a>
        <a class="fabu" @click="save()">保存</a>
      </div>
    </div>
    
    <div class="novel-title">
      <div class="container">
        <label>标题</label>
        <input type="text" v-model="title" placeholder="请输入小讲标题">
      </div>
    </div>
    <div class="novel-img">
      <div class="container">
        <img :src="imgUrl" title="＋点击请添加导图">
        <input type="file" @change='onUploadImg'>
      </div>
    </div>
    <div class="novel-title">
      <div class="container">
        <label>价格</label>
        <input type="text" v-model="price" placeholder="请输入价格">
      </div>
    </div>
    <div class="novel-cate">
      <div class="container"> 
        <label>类型</label>
        <select v-model="cate_id">
          <option value="0">请选择</option>
          <option v-for="item in smalltalk_cate" :value="item.id">{{item.cate_name}}</option>
        </select>
      </div>
    </div>
    <div class="novel-content">
      <div class="container"> 
        <label>内容</label>
        <textarea rows="10" placeholder="请介绍小讲内容" v-model="content"></textarea>
      </div>
    </div>

    <h2 class="nav_list_menu">
      <div class="container">
        <span>讲单</span>
        <mt-palette-button content="+" @click.native="show_addbox()">
          <div class="my-icon-button"></div>
        </mt-palette-button>
      </div>
    </h2>
    <div class="class_list">
      <div class="container">
        <div class="single-title" v-if="show">
          <mt-field placeholder="请输入章节" v-model="single_val">
            <mt-palette-button content="√" style="margin-right:0;" @click.native="single_value()">
              <div class="my-icon-button"></div>
            </mt-palette-button>
          </mt-field>
        </div>
        <ul class="class_topic">
          <li class="classcon_list" v-for="(item,index) in single_list">
            <div class="single" v-if="show_list">
              <p class="caption">{{index+1+"."}}{{item.name}}</p>
              <div class="btn">
                <button class="add-audio">添加内容
                  <input type="file" @change='onUpload'>
                </button>
                <mt-palette-button content="-" style="background-color: #f85f48;" @click.native="single_del(index)">
                  <div class="my-icon-button"></div>
                </mt-palette-button>
              </div>
            </div>
            <div class="single-content" v-if="show1" v-for="(itema,id) in item.sub_list">
                <div class="gre_title">
                  <span class="blue">试听</span>
                  <span class="title">{{itema.name}}</span>
                </div>
                <div class="timeapp">
                  <span class="bofang"></span>
                  <span class="time">01:26</span>
                </div>
                <mt-palette-button content="-" style="background-color: #f85f48;" @click.native="single_small_del(index,id)">
                  <div class="my-icon-button"></div>
                </mt-palette-button>
            </div> 
          </li>
        </ul>
      </div>
    </div>
    <div class="input_name">
      <mt-popup
        v-model="show_input"
        popup-transition="popup-fade">
        <input type="text" v-model="input_name" placeholder="请输入内容标题">
        <input type="button" value="保存" @click="yes" :disabled="disabled" :class="{'disabled':disabled}">
        <input type="button" value="取消" @click="no">
      </mt-popup>
    </div>
    <div class="tips">
      <mt-popup
        v-model="show_tips"
        popup-transition="popup-fade">{{tip}}
      </mt-popup>
    </div>
  </div>
</template>

<script type="es6">
import {mapState} from 'vuex'
import { Popup } from 'mint-ui'
import { PaletteButton } from 'mint-ui'
import {Toast} from 'mint-ui'

  export default {
    data () {
      return {
        show:false,
        show1:false,
        show_list:false,
        single_val:"",
        single_list:[],
        audio:{},
        show_input:false,
        input_name:'',
        id:-1,
        title:'',
        img:null,
        imgUrl:'',
        content:'',
        price:4.9,
        cate_id:0,
        show_tips:false,
        tip:'',
        smalltalk_cate:[]
      }
    },
    computed:{
      ...mapState(['info']),
      disabled: function(){
        return this.input_name.length <= 0
      }
    },
    mounted(){
      this.$http
      .get("smalltalkCate")
      .then(rtnData => {
        this.smalltalk_cate = rtnData.data;
      })
    },
    methods:{  
      show_addbox:function(){
        this.show=!this.show;
      },
      single_value:function(){
        if(!this.single_val){
          //Toast("请输入信息");
          this.show_tips = true;
          this.tip = '请输入信息';
        }else{
          this.show=false;
          this.show_list=true;
          this.single_list.push({name:this.single_val,sub_list:[]});
          this.single_val="";
          this.id++;
        }
      },
      single_del:function(index){
        this.single_list.splice(index,1);
      },
      single_small_del:function(index,id){
        this.single_list[index].sub_list.splice(id,1);
      },
      onUpload(e){
        this.audio = e.target.files[0];
        this.show_input = true;
      },
      onUploadImg(e){
        this.img = e.target.files[0];
        this.imgUrl = window.URL.createObjectURL(this.img);
      },
      yes(e){
        e.stopPropagation();
        this.single_list[this.id].sub_list.push({name:this.input_name,audio:this.audio});
        this.show1 = true;
        this.show_input = false;
        this.input_name = '';
      },
      no(e){
        e.stopPropagation();
        this.audio = {};
        this.show_input = false;
        this.input_name = '';
      },
      touhome(){
        this.$router.push('/uhome');
      },
      save:function(){
        if(!this.title){
          this.show_tips = true;
          this.tip = '请输入标题';
        }else if(!this.img){
          this.show_tips = true;
          this.tip = '请添加导图';
        }else if(!this.price){
          this.show_tips = true;
          this.tip = '请输入价格';
        }else if(this.price < 1){
          this.show_tips = true;
          this.tip = '价格不低于1元';
        }else if(this.cate_id == 0){
          this.show_tips = true;
          this.tip = '请选择类型';
        }else if(!this.content){
          this.show_tips = true;
          this.tip = '请输入内容';
        }else if(!(this.single_list!=[] && this.single_list.length>0)){
          this.show_tips = true;
          this.tip = '请添加小讲';
        }else{
          this.$http.interceptors.request.eject(this.$myInterceptor);
          let fd = new FormData();
          this.single_list.forEach( function(element, index) {
            element.sub_list.forEach( function(ele, id) {
              fd.append('novel_'+index+'_'+id, ele.audio);
            });
          });
          //fd.append('type', 'novel');
          fd.append('title', this.title);
          fd.append('img', this.img);
          fd.append('price', this.price);
          fd.append('cate_id', this.cate_id);
          fd.append('content', this.content);
          fd.append('novel_list', JSON.stringify(this.single_list));
          fd.append('vid', this.info.vip_id);

          this.$http({
            method: "post",
            url:'smalltalk',
            data: fd,
            processData: false
          }).then(rtnData => {
            this.show_tips = true;
            this.tip = "添加成功";
            setTimeout(()=>{
              this.$router.push('/stalkteacher/'+rtnData.data);
            }, 1000)
          });
        }
      }
    }
  }
</script>
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style >

  @import '../assets/css/novel.css';
  
</style>