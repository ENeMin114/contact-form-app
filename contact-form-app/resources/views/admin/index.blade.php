<x-app-layout>
    <x-slot name="header">
        <form action="/logout" method="post">
            @csrf
            <button class="px-5 py-1.5 border border-[#ddd8d3] text-[#c4bab0] bg-white rounded hover:bg-gray-50 transition lowercase text-sm">logout</button>
        </form>
    </x-slot>

    <!-- 🔴 Alpine.jsでポップアップ（モーダル）の開閉状態を管理します -->
    <div x-data="{ openModal: null }" class="min-h-screen bg-white py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-center text-2xl font-serif text-amber-900 mb-6">Admin</h2>

            <!-- 検索フォーム -->
            <div class="mb-4">
                <form class="flex flex-wrap items-center gap-3" action="/admin" method="get">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="名前やメールアドレスを入力してください" class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-gray-700 placeholder-[#c4bab0] focus:outline-none focus:border-amber-500" />
                    </div>
                    <div class="min-w-[100px]">
                        <select name="gender" class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-[#9a938c] focus:outline-none focus:border-amber-500">
                            <option value="0" {{ request('gender') == '0' || !request('gender') ? 'selected' : '' }}>性別</option>
                            <option value="1" {{ request('gender') == '1' ? 'selected' : '' }}>男性</option>
                            <option value="2" {{ request('gender') == '2' ? 'selected' : '' }}>女性</option>
                            <option value="3" {{ request('gender') == '3' ? 'selected' : '' }}>その他</option>
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <select name="category_id" class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-[#9a938c] focus:outline-none focus:border-amber-500">
                            <option value="">お問い合わせの種類</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->content }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[130px]">
                        <input type="date" name="date" value="{{ request('date') }}" placeholder="年/月/日" class="w-full px-4 py-2 bg-white border border-[#ddd8d3] rounded text-[#9a938c] focus:outline-none focus:border-amber-500" />
                    </div>
                    <div>
                        <button type="submit" class="px-6 py-2 bg-[#82746a] text-white rounded hover:bg-[#6b5f57]">検索</button>
                    </div>
                    <div>
                        <a href="/admin" class="px-6 py-2 bg-[#e8ddd2] text-[#9a938c] rounded hover:bg-[#ddd2c7] inline-block">リセット</a>
                    </div>
                    <div>
                        <a href="/contacts/export?{{ http_build_query(request()->query()) }}" class="px-6 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 inline-block">エクスポート</a>
                    </div>
                    <div class="flex items-center">
                        {{ $contacts->appends(request()->query())->links() }}
                    </div>
                </form>
            </div>

            <!-- テーブル -->
            <div class="bg-white rounded overflow-hidden border border-gray-200">
                <table class="w-full">
                    <thead>
                        <tr class="bg-[#a89e94]">
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">お名前</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">性別</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">メールアドレス</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">お問い合わせの種類</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white">タグ</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-white"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($contacts as $contact)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contact->first_name }} {{ $contact->last_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @php $genderLabels = [1 => '男性', 2 => '女性', 3 => 'その他']; @endphp
                                    {{ $genderLabels[$contact->gender] ?? '' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contact->email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $contact->category->content ?? '' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if(method_exists($contact, 'tags'))
                                        @foreach ($contact->tags as $tag)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1">{{ $tag->name }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <!-- 🔴 ボタンをクリックした時に、この行のIDをAlpine.jsに渡してモーダルを起動します -->
                                    <button @click="openModal = {{ $contact->id }}" class="text-amber-600 hover:text-amber-800 focus:outline-none">詳細</button>
                                </td>
                            </tr>

                            <!-- 🔴 ポップアップモーダル本体 -->
                            <div x-show="openModal === {{ $contact->id }}" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                <div class="fixed inset-0 bg-black bg-opacity-40 transition-opacity" @click="openModal = null"></div>
                                <div class="flex min-h-full items-center justify-center p-4">
                                    <div class="relative w-full max-w-2xl transform rounded bg-white p-8 shadow-xl border border-gray-200 text-gray-700">
                                        <button @click="openModal = null" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                                        <h3 class="text-xl font-serif text-[#6b5744] text-center mb-8">お問合せ詳細</h3>
                                        <div class="space-y-4 text-left">
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">お名前</span><span class="col-span-2 text-sm">{{ $contact->first_name }} {{ $contact->last_name }}</span></div>
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">性別</span><span class="col-span-2 text-sm">{{ $genderLabels[$contact->gender] ?? '' }}</span></div>
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">メールアドレス</span><span class="col-span-2 text-sm">{{ $contact->email }}</span></div>
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">電話番号</span><span class="col-span-2 text-sm">{{ $contact->tel }}</span></div>
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">住所</span><span class="col-span-2 text-sm">{{ $contact->address }}</span></div>
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">建物名</span><span class="col-span-2 text-sm">{{ $contact->building ?? '—' }}</span></div>
                                            <div class="grid grid-cols-3 border-b border-gray-100 pb-3"><span class="text-sm font-medium text-[#6b5744]">お問い合わせの種類</span><span class="col-span-2 text-sm">{{ $contact->category->content ?? '' }}</span></div>
                                            <div class="grid grid-cols-3 pb-3"><span class="text-sm font-medium text-[#6b5744]">お問い合わせ内容</span><span class="col-span-2 text-sm whitespace-pre-wrap">{{ $contact->detail }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">データがありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
