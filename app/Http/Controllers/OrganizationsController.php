<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Organization;

class OrganizationsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 列出所有公司清單
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show 公司申請表單
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * 新增公司資料
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display 單一公司資料
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show 編輯公司表單
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 更新公司資料
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 移除公司
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // 搜尋公司是否已存在
    public function search(Request $request)
    {
        
        $organization = new Organization();
        if ($organization->search($request->organization))
        {
            return response()->json(["result" => "get"]);
        }
        else 
        {
            return response()->json(["result" => "none"]);
        }
    }

    
}
