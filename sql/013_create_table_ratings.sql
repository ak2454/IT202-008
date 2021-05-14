CREATE TABLE Ratings(
    id int auto_increment,
    product_id int,
    user_id int,
    rating int,
    comment VARCHAR(100),
    created     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    primary key (id),
    foreign key (user_id) references Users(id),
    foreign key (product_id) references Products(id)
)
