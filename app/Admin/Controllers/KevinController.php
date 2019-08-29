<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MyPic;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KevinController extends Controller
{
    public function list(Content $content){
        $grid = new Grid(new MyPic());

        $grid->quickSearch('name');
        $grid->column('id', 'ID')->sortable('desc');
        $grid->column('name', '名称');

        $grid->column('url', '图片')
            ->image('/', 100, 100)
            ->modal('IMG', function ($model) {
                return '<img src="'.$model['url'].'" style="width:100%;height:100%;">';
            });

        $grid->column('created_at', '创建时间');

        $grid->actions(function ($actions) {
            $actions->disableEdit();
        });

        return $content
            ->header('MyPic')
            ->description('图片列表')
            ->body($grid);
    }

    public function create(Content $content)
    {
        $form = new Form(new MyPic());

        $form->setAction('/admin/kevin/save');

        $form->text('name', '名称');
        $form->multipleFile('img_url', '文件');
        $form->datetime('created_at');

        return $content
            ->header('Create')
            ->description('上传图片')
            ->body($form);
    }

    public function save(Request $request){
        $files = $request->file('img_url');
        foreach ($files as $file){
            print_r($file);
            $fileName = $this->upload($file);
            if ($fileName){
                $pic = new MyPic();
                $pic['name'] = $request['name'] ? $request['name'] : '未命名_'.time();
                $pic['url'] = $fileName;

                if(!$pic->save()){
                    return '保存失败';
                }
            }else{
                return '上传失败';
            }
        }

        return redirect('/admin/kevin/');
    }

    /**
     * 验证文件是否合法
     */
    public function upload($file, $disk='public') {
        // 1.是否上传成功
        if (!$file->isValid()) {
            return false;
        }

        // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
        $fileExtension = $file->getClientOriginalExtension();
        if(! in_array($fileExtension, ['png', 'jpg', 'gif'])) {
            return false;
        }

        // 3.判断大小是否符合 2M
        $tmpFile = $file->getRealPath();
        if (filesize($tmpFile) >= 2048000) {
            return false;
        }

        // 4.是否是通过http请求表单提交的文件
        if (! is_uploaded_file($tmpFile)) {
            return false;
        }

        // 5.每天一个文件夹,分开存储, 生成一个随机文件名
        $fileName = date('Y_m_d').'/'.md5(time()) .mt_rand(0,9999).'.'. $fileExtension;
        if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile)) ){
            return Storage::url($fileName);
        }
    }

}
