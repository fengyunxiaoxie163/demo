<?php
require_once './function/c_system_base.php';

$zbp->Initialize();

$action=GetVars('act','GET');

if (!CheckRights($action)) {throw new Exception($GLOBALS['lang']['error'][6]);}

switch ($action) {
	case 'login':
		redirect('login.php');
		break;
	case 'logout':
		Logout();
		redirect('../');
		break;
	case 'admin':
		redirect('admin/');
		break;	
	case 'vrs':
		# code...
		break;
	case 'verify':
		Login();
		break;
	case 'reload':
		echo Reload(GetVars('QUERY_STRING','SERVER'));
		break;
	case 'ArticleEdt':
		redirect('admin/edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryMng':
		redirect('admin/category.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryEdt':
		redirect('admin/category_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	case 'CategoryPst':
		CategoryPost();
		//redirect('admin/category_edit.php?' . GetVars('QUERY_STRING','SERVER'));
		break;
	default:
		# code...
		break;
}

$zbp->Terminate();

/*
Select Case strAct

	'命令列表

	Case "login" 

		Call BlogLogin()

	Case "verify"

		Call BlogVerify()

	Case "logout"

		Call BlogLogout()

	Case "admin" 

		Call BlogAdmin()

	Case "cmt"

		Call CommentPost()

	Case "tb"
		Call TrackBackPost()

	Case "vrs"
		Call ViewRights()

	Case "ArticleMng"

		Call ArticleMng()

	Case "ArticleEdt"

		Call ArticleEdt()

	Case "ArticlePst"

		Call ArticlePst()

	Case "ArticleDel"

		Call ArticleDel()

	Case "CategoryMng"

		Call CategoryMng()

	Case "CategoryEdt"

		Call CategoryEdt()

	Case "CategoryPst"

		Call CategoryPst()

	Case "CategoryDel"

		Call CategoryDel()

	Case "CommentMng"

		Call CommentMng()

	Case "CommentDel"

		Call CommentDel()

	Case "CommentEdt"

		Call CommentEdt()

	Case "CommentSav"

		Call CommentSav()

	Case "CommentGet"

		Call CommentGet()

	Case "CommentAudit"
		
		Call CommentAudit()

	Case "TrackBackMng"

		Call TrackBackMng()

	Case "TrackBackDel"

		Call TrackBackDel()

	Case "TrackBackSnd"

		Call TrackBackSnd()

	Case "UserMng"

		Call UserMng()

	Case "UserCrt"

		Call UserCrt()

	Case "UserEdt"

		Call UserEdt()

	Case "UserMod"

		Call UserMod()

	Case "UserDel"

		Call UserDel()

	Case "FileMng"

		Call FileMng()

	Case "FileSnd"

		Call FileSnd()

	Case "FileUpload"

		Call FileUpload()

	Case "FileDel"

		Call FileDel()

	Case "Search"

		Call Search()

	Case "SettingMng"

		Call SettingMng()

	Case "SettingSav"

		Call SettingSav()

	Case "TagMng"

		Call TagMng()

	Case "TagEdt"

		Call TagEdt()

	Case "TagPst"

		Call TagPst()

	Case "TagDel"

		Call TagDel()

	Case "PlugInMng"

		Call PlugInMng()

	Case "SiteInfo"

		Call SiteInfo()

	Case "SiteFileMng"

		Call SiteFileMng()

	Case "SiteFileEdt"

		Call SiteFileEdt()

	Case "SiteFilePst"

		Call SiteFilePst()

	Case "SiteFileDel"

		Call SiteFileDel()


	Case "gettburl"
		Call TrackBackUrlGet()

	Case "CommentDelBatch"

		Call CommentDelBatch()

	Case "TrackBackDelBatch"

		Call TrackBackDelBatch()

	Case "FileDelBatch"

		Call FileDelBatch()

	Case "ThemeMng"

		Call ThemeMng()

	Case "ThemeSav"

		Call ThemeSav()


	Case "LinkMng"

		Call LinkMng()

	Case "LinkSav"

		Call LinkSav()


	Case "PlugInActive"

		Call PlugInActive()

	Case "PlugInDisable"

		Call PlugInDisable()

	Case "FunctionMng"

		Call FunctionMng()

	Case "FunctionEdt"

		Call FunctionEdt()

	Case "FunctionSav"

		Call FunctionSav()

	Case "FunctionDel"

		Call FunctionDel()

	Case "AskFileReBuild"

		Call AskFileReBuild()

	Case "BlogReBuild"

		Call BlogReBuild()

	Case "FileReBuild"

		Call FileReBuild()

	Case "batch"

		Call Batch()

End Select
*/
?>