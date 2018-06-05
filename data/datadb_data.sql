delete from tb1;

delete from tb2;

insert into tb1 (id, name, age) values(1, 'name11', 10);
insert into tb1 (id, name, age) values(2, 'name12', 21);
insert into tb1 (id, name, age) values(3, 'name13', 32);
insert into tb1 (id, name, age) values(4, 'name14', null);
insert into tb1 (id, name, age) values(5, null, 22);

insert into tb2 (id, name, dob) values('id21', 'name21', '1977-01-01');
insert into tb2 (id, name, dob) values('id22', 'name22', '2010-03-19');
insert into tb2 (id, name, dob) values('id23', 'name23', null);
insert into tb2 (id, name, dob) values('id24', null, '2017-01-01');
insert into tb2 (id, name, dob) values('id25', null, null);
