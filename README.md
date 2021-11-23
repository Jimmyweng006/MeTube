# MeTube

###### tags: `Backend`

## 架構相關

### watch.php

commentSection

## 處理細節

### 處理上傳的影片檔案並將影片格式統一為.mp4

1. 將videoData搬到tempFilePath的位置。
2. 將video對應的資料insert到videos table中。
3. tempFilePath的video轉成finalFilePath，並且刪除原本的tempFilePath video。
    1. https://iter01.com/601800.html
    2. m1的話先裝ffmpeg：brew install homebrew-ffmpeg/ffmpeg/ffmpeg
    3. issue：sh: ffmpeg: command not found。虛驚一場，先找到ffmpeg的path即可。
4. 決定影片的縮圖(thumbnails)
    1. 使用($duration * 0.8) / $numThumbnails * $num，以防止縮圖選到影片的開頭或結尾。
    2. 檔案資料夾記得要開啟所有人的read/write權限。
    3. modal會跳掉 => 照順序加下面這兩行
    *  $('#loadingModal').modal({backdrop:'static', keyboard:false});
    *  $("#loadingModal").modal("show");

### 註冊帳號

1. sanitize input string
    1. strip space
    2. lowercase all char
    3. uppercase first char
2. validate sanitized string
    1. password only contains letter or number(regular expression)
3. get user's entered values after signing up fail or resubmit => take values from $_POST
4. hashing password => sha512
5. session：a way of keeping track whether user logged in or not
6. sign in的username跟password也要做sanitize（？

### 使用者資料

1. 把User的instance放在header.php裡方便讓有require header.php的檔案都能使用。
2. 把username相關的資料從users table抓下來，存到$sqlData裡。

### 影片相關

1. 將wacth.php切成四個區塊
    * 左邊：video、videoInfo、comment section
    * 右邊：suggestion videos
    * 結構(外到內)：watch >> VideoInfoSection >> VideoInfoControls >> ButtonProvider
2. update views = views + 1 in database and $sqlData
3. autoplay失效？？？
4. 將create button這個function封裝成一個ButtonProvider的class以方便複用
5. like/dislike
    1. 各別弄一個table(likes/dislikes)，每次從裡面count對應的videoId有幾個like/dislike，好像有點慢。
    2. 新增likes/dislikes column在videos table裡，從O(n) -> O(1)。但是action的js就要改一改了。
    3. ajax：php code will not be executed after page is loaded => use ajax to call another php file(thumb up/down without refreshing the page)
    * business logic：button跟videoPlayerAction.js綁在一起，當button press發生後，videoPlayerAction.js會發post ajax到likeVideo.php，likeVideo.php則是會去call $video->like()。
    * $video->like()會根據當前user的like/dislike情況回傳一個JSON，再用這個JSON update當前likes/dislikes number。
6. subscribe/edit button(如果當前的影片是自己上傳的話，就顯示edit button，不然就顯示subscribe button)

### 評論相關

1. tables：responseTo為0是一般評論，不然則是對特定的評論做評論。
2. insert query之後做$con->lastInsertId()，這中間不能放入別的sql(像是select)不然會出事啊...
3. Comment logic：commentActions.js >> postComment.php >> Comment.php >> CommentControls.php
4. Comment on other Comment：把CommentSection那套搬過來用
5. toggleReply()：在createReplyButton和createReplySection都會用到，用來操作hidden這個css class。
6. 展示影片的所有評論(responseTo = 0)：拿到所有comment的資料，並用該列資料new一個Comment的instance，把這些instances塞到array裡，之後就可以各別call create()。
7. 查看對某評論的所有評論：把Video.php的getComments那套搬過來改
8. postComment：如果replyTo為null(對影片評論)，那會把comment加到comments這個唯一的class(新到舊)；如果replyTo非null(對評論評論)，找到跟button的parent同一層的siblings(.repliesSection)，然後把comment加到repliesSection這個唯一的class(舊到新)。

### 影片方格顯示相關

1. 結構(外到內)：VideoGrid >> VideoGridItem
2. 影片來源：根據$videoGrid->create()的第一個參數決定
3. subscriptionVideos：用subscriptions list組出uploadedby = user1 OR user2 OR user3...的SQL。
4. SubscriptionProvider的bindParam要改成bindValue???

### 搜尋影片相關
1. 預設是依照觀看次數多到少排序
2. 搜尋條件：title/description LIKE CONCAT('%', :term, '%')
3. 搜尋影片結果，依據filter的條件排序

### EC2相關

#### 設定

https://derek.coderbridge.io/2020/09/16/create-your-website/
照著做無煩惱，接下來就是搬移資料庫的問題了。

#### 搬資料庫

結果根本不用直接從local scp檔案到server啊...
https://www.itread01.com/content/1544456170.html

匯出local的資料庫(MeTube)，然後用
scp -i ~/.ssh/MeTube.pem ~/Documents/MeTube.sql ec2-user@{ec2 instance ip address}:/var/www/html
轉移資料，阿怎麼一直被permission deny啊啊啊

換成這樣就好了...
scp -r(加的話是傳送folder) -i ~/.ssh/MeTube.pem ~/Documents/MeTube.sql ubuntu@{ec2 instance ip address}:/var/www/html

有設定密碼的，config檔記得要改啊...

#### sql error地雷

https://stackoverflow.com/questions/48001569/phpmyadmin-count-parameter-must-be-an-array-or-an-object-that-implements-co

#### 設定https

基本上就是拿到這個SSL憑證(Secure Socket Layer certificate)即可。

概念：[reference](https://medium.com/@justinlee_78563/%E9%97%9C%E6%96%BCaws-ec2-%E8%A8%AD%E5%AE%9Ahttps-17c95bc30d4e)
php開發主要是看這個：[reference](https://itkb.ddns.net/%E5%A6%82%E4%BD%95%E5%9C%A8amazon-ec2%E4%BC%BA%E6%9C%8D%E5%99%A8%E4%B8%AD%E2%80%A7%E5%BB%BA%E7%AB%8B%E7%9A%84xampp%E7%B6%B2%E9%A0%81%E4%BC%BA%E6%9C%8D%E5%99%A8%E2%80%A7%E5%8A%A0%E5%85%A5%E3%80%8Elet/)
上面的連結，我在第15步卡關了，於是改用了下面這個，就成功了！
啟用 Apache SSL 功能：[reference](https://www.ioa.tw/AWS/EC2-Ubuntu-LetsEncrypt.html)

#### CD

不然每次都要手動刪除資料夾，再從github上clone下來，再放入uploads跟config.php，有點煩...

其實就只是需要EC2自動做git pull就能達成CD了吧（？

看來是不能直接git pull了QQ

檔案權限也改了，也把.ssh/authorized_keys複製到.ssh/authorized_keys2這了
怎麼還是GG...
ssh: handshake failed: ssh: unable to authenticate, attempted methods [none publickey], no supported methods remain

### Bug

1. 未登入的狀態下，watch.php的profilePic會出事
    * fix：修改getProfilePic跟createUserProfileButton即可。