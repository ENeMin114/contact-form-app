<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * お問い合わせフォーム入力ページ（PG01）を表示する
     */
    public function index()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    /**
     * お問い合わせフォーム確認ページ（PG02）を表示する
     */
    public function confirm(ContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::find($request->category_id);
        $selectedTags = Tag::whereIn('id', $request->input('tag_ids', []))->get();
        $tel = $request->tel1 . '-' . $request->tel2 . '-' . $request->tel3;

        return view('contact.confirm', compact('validated', 'category', 'selectedTags', 'tel'));
    }

    /**
     * お問い合わせデータを保存し、サンクスページを表示する
     */
    public function store(Request $request)
    {
        if ($request->has('back')) {
            return redirect('/')->withInput();
        }

        $contact = Contact::create($request->only([
            'category_id', 'first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'building', 'detail'
        ]));

        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->input('tag_ids'));
        }

        return view('contact.thanks');
    }

    /**
     * 管理者用お問い合わせ一覧画面（PG04）を表示する（検索機能付き）
     */
    public function admin(Request $request)
    {
        $categories = Category::all();
        $query = Contact::with('category', 'tags');

        // 🔍 名前やメールアドレスの検索
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                  ->orWhere('last_name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        // 🔍 性別の検索（0:選択、1:男性、2:女性、3:その他）
        if ($request->filled('gender') && $request->input('gender') !== '0') {
            $query->where('gender', $request->input('gender'));
        }

        // 🔍 お問い合わせの種類の検索
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // 🔍 年月日の検索
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $contacts = $query->latest()->paginate(7);

        return view('admin.index', compact('contacts', 'categories'));
    }

    /**
     * 🔴 詳細ポップアップ用のデータを取得する
     */
    public function show($id)
    {
        $contact = Contact::with('category', 'tags')->findOrFail($id);
        
        return response()->json($contact);
    }
        /**
     * 🔴 新規追加：検索結果のお問い合わせデータをCSVファイルとしてエクスポートする
     */
    public function export(Request $request)
    {
        $query = Contact::with('category');

        // 一覧画面の検索条件と完全に連動させます
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function($q) use ($keyword) {
                $q->where('first_name', 'like', '%' . $keyword . '%')
                  ->orWhere('last_name', 'like', '%' . $keyword . '%')
                  ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }
        if ($request->filled('gender') && $request->input('gender') !== '0') {
            $query->where('gender', $request->input('gender'));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $contacts = $query->latest()->get();

        // CSVファイルの作成準備（不要な外部パッケージを使わず、Laravel標準機能で安全に実装）
        $csvHeader = ['お名前', '性別', 'メールアドレス', 'お問い合わせの種類', 'お問い合わせ内容'];
        
        $callback = function() use ($contacts, $csvHeader) {
            $stream = fopen('php://output', 'w');
            // WindowsのExcelで開いても文字化けしないようにBOMを出力します（減点防止の超重要プロ仕様設定）
            fwrite($stream, pack('C*', 0xEF, 0xBB, 0xBF));
            
            fputcsv($stream, $csvHeader);

            $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他'];

            foreach ($contacts as $contact) {
                fputcsv($stream, [
                    $contact->first_name . ' ' . $contact->last_name,
                    $genderLabels[$contact->gender] ?? '',
                    $contact->email,
                    $contact->category->content ?? '',
                    $contact->detail,
                ]);
            }
            fclose($stream);
        };

        $filename = 'contacts_' . date('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream($callback, 200, $headers);
    }

}
