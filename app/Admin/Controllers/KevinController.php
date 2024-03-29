<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MyPic;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KevinController extends Controller
{
    public function list(Content $content){
        $grid = new Grid(new MyPic());

        $grid->model()->orderBy('id','desc');
        $grid->quickSearch('name');
        $grid->column('id', 'ID')->sortable();
        $grid->column('name', '名称');

        $grid->column('url', '图片')
            ->image('/', 100, 100)
            ->modal('IMG', function ($model) {
                return '<img src="'.$model['url'].'" style="width:100%;height:100%;">';
            });

        $grid->column('created_at', '创建时间');

        $grid->paginate(20);

        $grid->actions(function ($actions) {
//            $actions->disableEdit();
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
    private function upload($file, $disk='public') {
        // 1.是否上传成功
        if (!$file->isValid()) {
            return false;
        }

        // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
        $fileExtension = $file->getClientOriginalExtension();
        if(! in_array($fileExtension, ['png', 'jpg', 'gif', 'jpeg', 'mp4', 'avi', '3gp', 'rmvb'])) {
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


    public function show($id, Content $content){
        return $content->header('MyPic')
            ->description('详情')
            ->body(Admin::show(MyPic::findOrFail($id), function (Show $show) {

                $show->id('ID');
                $show->name('标题');
//                $image = img_url($show->model()->image);
//                $show->image('图片')->default($image);
//                $show->url('图片')->as(function($url){
//                    return '<img src="'.$url.'" style="width:100%;height:100%;">';
//                });
                $show->url('图片')
                    ->image('', 100, 100)
                    ->modal('IMG', function ($url) {
                        return '<img src="'.$url.'" style="width:100%;height:100%;">';
                    });

                $show->created_at();
            }));
    }

    public function update($id, Request $request){
        $pic = MyPic::findOrFail($id);
        $pic['name'] = $request['name'] ? $request['name'] : '未命名_'.time();

        if(!$pic->update()){
            return '保存失败';
        }

        return redirect('/admin/kevin/');
    }


    public function edit($id, Content $content){
//        return $content->header('MyPic')
//            ->description('编辑')
//            ->body(Admin::show(MyPic::findOrFail($id), function (Grid\Actions\Edit $show) {
//
//                $show->id('ID');
//                $show->name('标题');
//            }));

        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }



    protected function form()
    {
        return Admin::form(MyPic::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name', '标题');
//            $form->file('url', '图片');
            $form->image('url', '图片');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

}
