CREATE TABLE Orders(
    id int auto_increment,
    user_id int,
    total_price int,
    address VARCHAR(60),
    Payment VARCHAR(10),
    modified    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP on update current_timestamp,
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    primary key (id),
    foreign key (user_id) references Users(id)
)
