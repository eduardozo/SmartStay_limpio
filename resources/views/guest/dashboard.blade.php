@extends("layout")
@section("content")



    <transition name="fade">
        <div id="sliderServices" v-if="!show">
            <tiny-slider :mouse-drag="true" :loop="false" items="1" gutter="100">
                <div class="containerServices">
                    <serviceshome v-bind:nserv="0" v-bind:servs="[1,1,1,1]" v-bind:services="services"></serviceshome>
                </div>
                <div class="containerServices">
                    <serviceshome v-bind:nserv="4" v-bind:servs="[1,1,1,0]" v-bind:services="services"></serviceshome>
                </div>
            </tiny-slider>
        </div>
    </transition>


    <div class="windows">
        <transition name="fade">
            <div class="windowService" v-if="window[0]">

                <div class="windowTitle">
                    <button class="returnWindow " v-if="show" @click="showWindow(0)"><i class="fas fa-long-arrow-alt-left"></i></button>
                    <h2>@{{ services[0].name }}</h2>
                </div>

                <p class="windowDesc">@{{ services[0].description }}</p>
                <div class="windowContent row">
                    <form class="attribOrder col-md-7">
                        <label for="dateRestaurant"> Day and hour: </label>
                        <input type="datetime-local" name="dateRestaurant"><br>
                        <label for="numPersonRest"> Number of persons: </label> <input type="number" name="numPersonRest">
                        <br>
                        <input type="submit" name="enviar">
                    </form>
                    <div class="resultOrder col-md-5">
                        <p>Booking name: <strong>{{\App\Guest::find(Session::get('guest_id'))->rooms[0]->number}}</strong></p>
                    </div>


                </div>
            </div>
            <div class="windowService" v-if="window[1]">
                <div class="windowTitle">
                    <button class="returnWindow " v-if="show" @click="showWindow(1)"><i class="fas fa-long-arrow-alt-left"></i></button>
                    <h2>@{{ services[1].name }}</h2>
                </div>
                <p class="windowDesc">@{{ services[1].description }}</p>
                <div class="windowContent">
                    Snack: <select name="snackOrder" id="snackOrder">
                        <option>Select a snack</option>
                        <option value="">Select a snack</option>

                    </select>
                    Drink: <select name="drinkOrder" id="drinkOrder">
                        <option value="">Select a drink</option>
                    </select>
                </div>

            </div>
            <div class="windowService" v-if="window[2]">
                <div class="windowTitle">
                    <button class="returnWindow " v-if="show" @click="showWindow(2)"><i class="fas fa-long-arrow-alt-left"></i></button>
                    <h2>@{{ services[2].name }}</h2>
                </div>
                <p class="windowDesc">@{{ services[2].description }}</p>
                <div class="windowContent">
                Day: <input type="date">
                Hour: <input type="hour">
                Type: <select name="" id=""></select>
                Booking name: <input type="text">
                </div>

            </div>
            <div class="windowService" v-if="window[3]">
                <div class="windowTitle">
                    <button class="returnWindow " v-if="show" @click="showWindow(3)"><i class="fas fa-long-arrow-alt-left"></i></button>
                    <h2>@{{ services[3].name }}</h2>
                </div>
                <p class="windowDesc">@{{ services[3].description }}</p>
                <div class="windowContent">
                Day: <input type="date">
                Hour: <input type="hour">
                </div>
            </div>
            <div class="windowService" v-if="window[4]">
                <div class="windowTitle">
                    <button class="returnWindow " v-if="show" @click="showWindow(4)"><i class="fas fa-long-arrow-alt-left"></i></button>
                    <h2>Pet care</h2>
                </div>
                <p class="windowDesc">Pet care description</p>
                <div class="windowContent">
                    <input type="checkbox"> Water
                    <input type="radio"> Standard food
                    <input type="radio"> Premium food
                    Snacks: <input type="hour">
                </div>
            </div>
            <div class="windowService" v-if="window[5]">
                <div class="windowTitle">
                    <button class="returnWindow " v-if="show" @click="showWindow(5)"><i class="fas fa-long-arrow-alt-left"></i></button>
                    <h2>Trips</h2>
                </div>
                <p class="windowDesc">Trip description</p>
                <div class="windowContent">

                    Choose a trip: <select name="" id="" v-model="tripSelected">
                        <option v-for="trip in trips">@{{ trip.name }}</option>

                    </select>
                    Number of persons: <input type="number" min="1" v-model="numPersonsTrip">

                    <div class="windowInfo" v-for="item in infoTrip" v-if="tripSelected != ''">
                        <p>Location: @{{ item.location }}</p>
                        <p>Day: @{{ item.day_week }}</p>
                        <p>Price: @{{  setPriceTrip(item.price) }}</p>

                    </div>

                </div>
            </div>
            <div class="windowService" v-if="window[6]">
            <div class="windowTitle">
                <button class="returnWindow " @click="showWindow(6)"><i class="fas fa-long-arrow-alt-left"></i></button>
                <h2>@{{ services[6].name }}</h2>
            </div>
            <p class="windowDesc">@{{ services[6].description }}</p>
            <div class="windowContent">
                Select a event: <select name="" id="">
                    <option value=""></option>
                </select>

            </div>
    </div>
        </transition>
    </div>


<footer v-if="show">  </footer>

    {{-- ------------------ --}}









@endsection