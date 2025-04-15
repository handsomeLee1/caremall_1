# 카테고리 관리 시스템

## 기능 설명

### 1. 카테고리 관리
- 상위 카테고리 추가/수정/삭제
- 하위 카테고리 추가/수정/삭제
- 카테고리 중복 체크
- 계층 구조로 카테고리 표시

### 2. 주요 기능
- 상위/하위 카테고리 구분
- 카테고리 추가 시 중복 검사
- 상위 카테고리 삭제 시 하위 카테고리 자동 삭제
- 직관적인 UI/UX

## 설치 방법

1. 데이터베이스 설정:
   - `db_connect.php` 파일에서 데이터베이스 연결 정보 수정
   ```php
   $servername = "localhost";
   $username = "your_username";
   $password = "your_password";
   $dbname = "your_database";
   ```

2. 테이블 구조:
   ```sql
   -- categories 테이블
   CREATE TABLE categories (
     카테고리ID int(11) NOT NULL AUTO_INCREMENT,
     카테고리명 varchar(50) NOT NULL,
     PRIMARY KEY (카테고리ID)
   );

   -- subcategories 테이블
   CREATE TABLE subcategories (
     세부분류ID int(11) NOT NULL AUTO_INCREMENT,
     세부분류명 varchar(50) NOT NULL,
     카테고리ID int(11) NOT NULL,
     PRIMARY KEY (세부분류ID),
     FOREIGN KEY (카테고리ID) REFERENCES categories(카테고리ID)
   );
   ```

## 사용 방법

1. 상위 카테고리 추가:
   - 상위 카테고리 선택 없이 카테고리명만 입력
   - "카테고리 추가" 버튼 클릭

2. 하위 카테고리 추가:
   - 상위 카테고리 선택
   - 하위 카테고리명 입력
   - "카테고리 추가" 버튼 클릭

3. 카테고리 수정:
   - 해당 카테고리의 "수정" 버튼 클릭
   - 새로운 이름 입력
   - "수정 완료" 버튼 클릭

4. 카테고리 삭제:
   - 해당 카테고리의 "삭제" 버튼 클릭
   - 확인 메시지에서 "확인" 선택 