import Vue from 'vue'
import Router from 'vue-router'
import index from '@/view/index'
const Headline = resolve => require(['@/view/headline'], resolve)
const HeadlineDetails = resolve => require(['@/view/headline-details'], resolve)
const FindExpert = resolve => require(['@/view/findExpert'], resolve)
const Category = resolve => require(['@/view/category'], resolve)
const TutorDetails = resolve => require(['@/view/tutorDetails'], resolve)
const Listen = resolve => require(['@/view/listen'], resolve)
const ProblemDetails = resolve => require(['@/view/problemDetails'], resolve)
const AskQuickly = resolve => require(['@/view/ask-quickly'], resolve)
const Rewards = resolve => require(['@/view/rewards'], resolve)
const RewardDetails = resolve => require(['@/view/reward-details'], resolve)
const RewardWrap = resolve => require(['@/view/reward-wrap'], resolve)
const Purchase = resolve => require(['@/view/purchase'], resolve)
const CompleteList = resolve => require(['@/view/completeList'], resolve)
const AnswerToSubmit = resolve => require(['@/view/answerToSubmit'], resolve)
const Login = resolve => require(['@/view/login'], resolve)
import uhome from '@/view/uhome'
const edit = resolve => require(['@/view/edit'], resolve)
const answer = resolve => require(['@/view/answer'], resolve)
const question = resolve => require(['@/view/question'], resolve)
const openvip = resolve => require(['@/view/openvip'], resolve)
const publish = resolve => require(['@/view/publish'], resolve)
const totrue = resolve => require(['@/view/totrue'], resolve)
const novel = resolve => require(['@/view/novel'], resolve)
const resetPhone = resolve => require(['@/view/reset-phone'], resolve)
const ask = resolve => require(['@/view/ask'], resolve)
const help = resolve => require(['@/view/help'], resolve)
const set = resolve => require(['@/view/set'], resolve)
const talk = resolve => require(['@/view/talk'], resolve)
const stalkcon = resolve => require(['@/view/stalkcon'], resolve)
const stalkteacher = resolve => require(['@/view/stalkteacher'], resolve)
const shiting = resolve => require(['@/view/shiting'], resolve)
const allspeech = resolve => require(['@/view/allspeech'], resolve)
const showresult = resolve => require(['@/view/showresult'], resolve)
const myQuickask = resolve => require(['@/view/my-quickask'], resolve)
const SmalltalkComments = resolve => require(['@/view/SmalltalkComments'], resolve)

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      component: index,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/headline/:id',
      component: Headline,
      meta: {
        title: '一元成才头条'
      }
    },
    {
      path: '/headline-details/:id',
      component: HeadlineDetails,
      meta: {
        title: '一元成才头条'
      }
    },
    {
      path: '/findExpert',
      component: FindExpert,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/category/:id',
      component: Category
    },
    {
      path: '/tutorDetails/:id',
      component: TutorDetails,
      meta: {
        title: ''
      }
    },
    {
      path: '/listen',
      component: Listen,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/problemDetails/:id',
      component: ProblemDetails,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/ask-quickly',
      component: AskQuickly,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/rewards/:id',
      component: Rewards,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/reward-details/:id',
      component: RewardDetails,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/reward-wrap/:id',
      component: RewardWrap,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/purchase',
      component: Purchase,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/completeList/:type',
      component: CompleteList,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/answerToSubmit',
      component: AnswerToSubmit,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/login',
      component: Login,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/uhome',
      component: uhome,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/edit',
      component: edit,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/answer',
      component: answer,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/question',
      component: question,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/openvip',
      component: openvip,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/publish',
      component: publish,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/totrue',
      component: totrue,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/novel',
      component: novel,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/reset-phone',
      component: resetPhone,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/ask',
      component: ask,
      meta: {
        title: '一元成才社区'
      }
    },
    {
      path: '/help',
      component: help,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/set',
      component: set,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/talk',
      component: talk,
      meta: {
        title: '一元成才小讲'
      }
    },
    {
      path: '/stalkcon/:id',
      component: stalkcon,
      meta: {
        title: '一元成才小讲'
      }
    },
    {
      path: '/stalkteacher/:id',
      component: stalkteacher,
      meta: {
        title: '一元成才小讲'
      }
    },
    {
      path: '/shiting',
      component: shiting,
      meta: {
        title: '一元成才小讲'
      }
    },
    {
      path: '/allspeech',
      component: allspeech,
      meta: {
        title: '一元成才小讲'
      }
    },
    {
      path: '/showresult',
      component: showresult,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/my-quickask/:id',
      component: myQuickask,
      meta: {
        title: '一元成才'
      }
    },
    {
      path: '/SmalltalkComments/:id',
      component: SmalltalkComments,
      meta: {
        title: '一元成才小讲'
      }
    }
  ]
})
