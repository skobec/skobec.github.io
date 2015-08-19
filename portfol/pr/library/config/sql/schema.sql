CREATE TABLE "public".economic_sphere ( 
	id                   serial  NOT NULL,
	title                varchar(700)  NOT NULL,
	CONSTRAINT economic_sphere_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".economic_sphere IS 'Отрасли';

COMMENT ON COLUMN "public".economic_sphere.id IS 'ID отрасли';

COMMENT ON COLUMN "public".economic_sphere.title IS 'Наименование отрасли';

CREATE TABLE "public".event_upload_form_type ( 
	id                   serial  NOT NULL,
	title                varchar(255)  NOT NULL,
	CONSTRAINT event_upload_form_type_pkey PRIMARY KEY ( id )
 );

CREATE TABLE "public".expenditure ( 
	id                   serial  NOT NULL,
	title                varchar(2048)  NOT NULL,
	"number"             varchar(4)  NOT NULL,
	CONSTRAINT expenditure_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".expenditure IS 'Направления расходов';

COMMENT ON COLUMN "public".expenditure.id IS 'ID направления рахода';

COMMENT ON COLUMN "public".expenditure.title IS 'Наименование направления рахода';

COMMENT ON COLUMN "public".expenditure."number" IS 'Четырехсимвольный код нправления расхода';

CREATE TABLE "public".inner_classification ( 
	id                   serial  NOT NULL,
	title                varchar(700)  NOT NULL,
	CONSTRAINT inner_classification_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".inner_classification IS 'Внутренняя классификация';

COMMENT ON COLUMN "public".inner_classification.id IS 'ID внутренней классфикации';

COMMENT ON COLUMN "public".inner_classification.title IS 'Наименование внутренней классфикации';

CREATE TABLE "public".ministry ( 
	id                   serial  NOT NULL,
	title                varchar(70)  NOT NULL,
	"number"             varchar(3)  NOT NULL,
	CONSTRAINT ministry_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".ministry IS 'Министерства';

COMMENT ON COLUMN "public".ministry.id IS 'ID министерства';

COMMENT ON COLUMN "public".ministry.title IS 'Наименование министерства';

COMMENT ON COLUMN "public".ministry."number" IS 'Трехсимвольный идентификатор министерства';

CREATE TABLE "public".spending_type ( 
	id                   serial  NOT NULL,
	title                varchar(700)  NOT NULL,
	"number"             varchar(3)  NOT NULL,
	CONSTRAINT spending_type_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".spending_type IS 'Виды расходов';

COMMENT ON COLUMN "public".spending_type.id IS 'ID вида расхода';

COMMENT ON COLUMN "public".spending_type.title IS 'Наименование вида расхода';

COMMENT ON COLUMN "public".spending_type."number" IS 'Трехсимвольный идентификатор раздела или подраздела';

CREATE TABLE "public"."user" ( 
	id                   serial  NOT NULL,
	login                varchar(255)  ,
	"password"           varchar(255)  ,
	CONSTRAINT user_pkey PRIMARY KEY ( id ),
	CONSTRAINT user_login_idx UNIQUE ( login ) 
 );

CREATE TABLE "public".division ( 
	id                   serial  NOT NULL,
	parent_division_id   integer  ,
	title                varchar(70)  NOT NULL,
	"number"             varchar(2)  NOT NULL,
	CONSTRAINT division_pk PRIMARY KEY ( id )
 );

CREATE INDEX division_parent_division_id_idx ON "public".division ( parent_division_id );

COMMENT ON TABLE "public".division IS 'Разделы и подразделы';

COMMENT ON COLUMN "public".division.id IS 'ID раздела или подраздела';

COMMENT ON COLUMN "public".division.parent_division_id IS 'ID родительского раздела';

COMMENT ON COLUMN "public".division.title IS 'Наименование раздела или подраздела';

COMMENT ON COLUMN "public".division."number" IS 'Двухсимвольный идентификатор раздела или подраздела';

CREATE TABLE "public".event ( 
	id                   serial  NOT NULL,
	spending_id          integer  NOT NULL,
	code                 varchar(13)  ,
	title                varchar(700)  NOT NULL,
	"number"             varchar(5)  NOT NULL,
	reciever_name        varchar(1024)  ,
	is_import_replacement smallint  ,
	economic_sphere_id   integer  ,
	CONSTRAINT event_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".event IS 'Мероприятия';

COMMENT ON COLUMN "public".event.id IS 'ID мероприятия';

COMMENT ON COLUMN "public".event.spending_id IS 'ID статьи расхода';

COMMENT ON COLUMN "public".event.code IS 'Код мероприятия';

COMMENT ON COLUMN "public".event.title IS 'Наименование мероприятия';

COMMENT ON COLUMN "public".event."number" IS 'Номер мероприятия';

COMMENT ON COLUMN "public".event.reciever_name IS 'Получатель средств';

COMMENT ON COLUMN "public".event.is_import_replacement IS 'Является ли импортозамещением?';

COMMENT ON COLUMN "public".event.economic_sphere_id IS 'ID отрасли';

CREATE TABLE "public".event_complete ( 
	id                   serial  NOT NULL,
	event_id             integer  ,
	event_code           varchar(13)  NOT NULL,
	title                varchar(700)  NOT NULL,
	dt                   date  NOT NULL,
	money_amount         numeric(-9999999,0)  NOT NULL,
	assignment_code      varchar(13)  NOT NULL,
	CONSTRAINT event_performance_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".event_complete IS 'Выполненые переводы средств';

COMMENT ON COLUMN "public".event_complete.id IS 'ID выполненного перевода средств';

COMMENT ON COLUMN "public".event_complete.event_code IS 'Код мероприятия';

COMMENT ON COLUMN "public".event_complete.title IS 'Наименование выполненного перевода средств';

COMMENT ON COLUMN "public".event_complete.dt IS 'Дата выполнения перевода средств';

COMMENT ON COLUMN "public".event_complete.money_amount IS 'Переведенный объем средств по мероприятию';

COMMENT ON COLUMN "public".event_complete.assignment_code IS 'Код поручения';

CREATE TABLE "public".event_plan ( 
	id                   serial  NOT NULL,
	event_id             integer  NOT NULL,
	event_year           smallint  NOT NULL,
	event_month          smallint  NOT NULL,
	money_amount         numeric(-9999999,0)  NOT NULL,
	CONSTRAINT event_plan_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".event_plan IS 'Заппланированные переводы средств';

COMMENT ON COLUMN "public".event_plan.id IS 'ID запланированного перевода средств';

COMMENT ON COLUMN "public".event_plan.event_id IS 'ID мероприятия';

COMMENT ON COLUMN "public".event_plan.event_year IS 'Год запланированного перевода средств';

COMMENT ON COLUMN "public".event_plan.event_month IS 'Месяц запланированного перевода средств';

COMMENT ON COLUMN "public".event_plan.money_amount IS 'Запланированный объем средств по мероприятию';

CREATE TABLE "public".program ( 
	id                   serial  NOT NULL,
	parent_program_id    integer  ,
	title                varchar(700)  NOT NULL,
	"number"             varchar(2)  NOT NULL,
	is_program           smallint  ,
	CONSTRAINT program_pk PRIMARY KEY ( id )
 );

CREATE INDEX program_parent_program_id_idx ON "public".program ( parent_program_id );

COMMENT ON TABLE "public".program IS 'Программы и подпрограммы';

COMMENT ON COLUMN "public".program.id IS 'ID программы или подпрограммы';

COMMENT ON COLUMN "public".program.parent_program_id IS 'ID родительской программы';

COMMENT ON COLUMN "public".program.title IS 'Наименование программы или подпрограммы';

COMMENT ON COLUMN "public".program."number" IS 'Двухсимвольный идентификатор программы или подпрограммы';

COMMENT ON COLUMN "public".program.is_program IS 'Программная(TRUE) или непрограммная(FALSE)';

CREATE TABLE "public".spending ( 
	id                   serial  NOT NULL,
	target_expend_id     integer  NOT NULL,
	ministry_id          integer  NOT NULL,
	division_id          integer  NOT NULL,
	spending_type_id     integer  NOT NULL,
	inner_classification_id integer  ,
	money_amount         numeric(-9999999,0)  ,
	created_at           integer  ,
	updated_at           integer  ,
	title                varchar(2048)  NOT NULL,
	CONSTRAINT spending_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".spending IS 'Статьи расходов';

COMMENT ON COLUMN "public".spending.id IS 'ID статьи расхода';

COMMENT ON COLUMN "public".spending.target_expend_id IS 'ID целевой статьи расхода';

COMMENT ON COLUMN "public".spending.ministry_id IS 'ID министерства';

COMMENT ON COLUMN "public".spending.division_id IS 'ID раздела';

COMMENT ON COLUMN "public".spending.spending_type_id IS 'ID вида расхода';

COMMENT ON COLUMN "public".spending.inner_classification_id IS 'ID внутренней классификации';

COMMENT ON COLUMN "public".spending.money_amount IS 'Объем средств по статье расхода';

COMMENT ON COLUMN "public".spending.created_at IS 'Дата добавления';

COMMENT ON COLUMN "public".spending.updated_at IS 'Дата последнего изменения';

CREATE TABLE "public".target_expend ( 
	id                   serial  NOT NULL,
	program_id           integer  NOT NULL,
	title                varchar(2048)  NOT NULL,
	expenditure_id       integer  NOT NULL,
	CONSTRAINT target_expend_pk PRIMARY KEY ( id )
 );

COMMENT ON TABLE "public".target_expend IS 'Целевые статьи расходов';

COMMENT ON COLUMN "public".target_expend.id IS 'ID целевой статьи расхода';

COMMENT ON COLUMN "public".target_expend.title IS 'Наименование целевой статьи расхода';

COMMENT ON COLUMN "public".target_expend.expenditure_id IS 'ID направления расходов';

ALTER TABLE "public".division ADD CONSTRAINT division_fk0 FOREIGN KEY ( parent_division_id ) REFERENCES "public".division( id );

ALTER TABLE "public".event ADD CONSTRAINT event_fk1 FOREIGN KEY ( economic_sphere_id ) REFERENCES "public".economic_sphere( id );

ALTER TABLE "public".event ADD CONSTRAINT event_fk0 FOREIGN KEY ( spending_id ) REFERENCES "public".spending( id );

ALTER TABLE "public".event_complete ADD CONSTRAINT event_complete_fk0 FOREIGN KEY ( event_id ) REFERENCES "public".event( id );

ALTER TABLE "public".event_plan ADD CONSTRAINT event_plan_fk0 FOREIGN KEY ( event_id ) REFERENCES "public".event( id );

ALTER TABLE "public".program ADD CONSTRAINT program_fk0 FOREIGN KEY ( parent_program_id ) REFERENCES "public".program( id );

ALTER TABLE "public".spending ADD CONSTRAINT spending_fk2 FOREIGN KEY ( division_id ) REFERENCES "public".division( id );

ALTER TABLE "public".spending ADD CONSTRAINT spending_fk4 FOREIGN KEY ( inner_classification_id ) REFERENCES "public".inner_classification( id );

ALTER TABLE "public".spending ADD CONSTRAINT spending_fk1 FOREIGN KEY ( ministry_id ) REFERENCES "public".ministry( id );

ALTER TABLE "public".spending ADD CONSTRAINT spending_fk3 FOREIGN KEY ( spending_type_id ) REFERENCES "public".spending_type( id );

ALTER TABLE "public".spending ADD CONSTRAINT spending_fk0 FOREIGN KEY ( target_expend_id ) REFERENCES "public".target_expend( id );

ALTER TABLE "public".target_expend ADD CONSTRAINT target_expend_fk1 FOREIGN KEY ( expenditure_id ) REFERENCES "public".expenditure( id );

ALTER TABLE "public".target_expend ADD CONSTRAINT target_expend_fk0 FOREIGN KEY ( program_id ) REFERENCES "public".program( id );

