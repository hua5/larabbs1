<?php

namespace App\Observers;

use App\Models\Topic;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        // XSS 过滤
        $topic->body = clean($topic->body, 'user_topic_body');

        // 生成话题摘录
        $topic->excerpt = make_excerpt($topic->body);
//      saving 为刚创建的时的事件 ，没有topic_id 所以改为saved时分发任务。
//        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
//        if ( ! $topic->slug) {
//
//            // 推送任务到队列
//            dispatch(new TranslateSlug($topic));
//        }
    }
   //saved 创建好的时候分发任务
    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug) {

            // 推送任务到队列
            dispatch(new TranslateSlug($topic));
        }
    }

    //监听话题删除成功的事件，在此事件发生时，我们会删除此话题下所有的回复：
    public function deleted(Topic $topic)
    {
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }

}