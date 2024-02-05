import React, { useEffect }  from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { ActivityIndicator, KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, TouchableOpacity, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function UpdateRestaurant(props) {
    useFocusEffect(
        React.useCallback(() => {
            const onBackPress = () => {
                goback();
                return true;
            };

            BackHandler.addEventListener(
                'hardwareBackPress', onBackPress
            );

            return () =>
                BackHandler.removeEventListener(
                    'hardwareBackPress', onBackPress
                );
        }, [])
    );

    useEffect(() => {
        const requestOptions = {
            method: "GET",
            headers: {
                "LETSEAT-AUTHKEY": props.user.user_token,
                "Content-Type": "application/json"
            },
        };
          
        fetch(global.server + "restaurant/read.php?id=" + props.restaurant,requestOptions).then(res => res.json()).then(result => {
            // setRestaurants(result)
            // console.log(result);
            setName(result.name);
            setLocation(result.location);
            setNotes(result.notes);
            return result
        })
          
    },[]);

    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [restaurants,setRestaurants] = React.useState([]);
    const [restaurantGroups,setGroupRestaurants] = React.useState([]);





    const toast = useToast();
    const [isLoadingName, setLoadingName] = React.useState(false);

    const notesRef = React.useRef(null);
    const locationRef = React.useRef(null);
    const [name, setName] = React.useState("");
    const [location, setLocation] = React.useState("");
    const [notes, setNotes] = React.useState("");
    const [isLoadingCreate, setLoadingCreate] = React.useState(false);


    const createRestaurant = () => {
        setLoadingCreate(true);
        if (name !== null && name !== undefined && name !== "") {
            const requestOptions = {
                method: "PUT",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id: props.restaurant,
                    name: name,
                    location: location,
                    notes: notes
                }),
            };

            fetch(global.server + "restaurant/update.php", requestOptions).then(res => res.json()).then(result => {
                if (result.result) {
                    alert("Restaurant Created Successfully!");
                    setLoadingCreate(false);
                    props.pagging(18);
                } else {
                    alert("Restaurant Not Created");
                    setLoadingCreate(false);
                }
            }).catch(() => {
                setTimeout(() => {
                    setLoadingCreate(false);
                    alert("Failed To Connect To Server");
                }, 2000);
            }).finally(() => {
                setTimeout(() => setLoadingCreate(false), 2000);
            });
        } else {
            toast.show("Please enter text into the field", {
                type: "warning",
                placement: "top",
                duration: 4000,
                offset: 100,
                animationType: "slide-in",
            });
            setLoadingCreate(false);
        }
    }
    const goback = () => {
        props.pagging(18)
    }

    const updateSelect = (id) => {
        setGroupRestaurants(restaurantGroups.map(restaurant => restaurant.id===id?{...restaurant,selected:!restaurant.selected}:restaurant));
    }

    return (
        <SafeAreaView>
            <View style={styles.main}>
                <View style={styles.navbar}>
                    <View style={styles.backbuttonview}>
                        <Button
                            textstyle={styles.backbutton}
                            title="Back"
                            onPress={goback}>
                        </Button>
                    </View>
                </View>
                <View style={styles.scrollView}>
                    <KeyboardAvoidingView
                        behavior={Platform.OS === "ios" ? "height" : "padding"}
                        enabled>
                        <ScrollView>
                            <View
                                style={styles.signin}
                            >
                                <Text style={styles.dashboardheader}>
                                    Update Restaurant
                                </Text>
                            <Text>Restaurant Name</Text>
                            <TextInput
                                style={styles.input}
                                onChangeText={setName}
                                value={name}
                                placeholder="John's Dough Hut"
                                onSubmitEditing={() => {
                                    locationRef.current.focus();
                                }}
                            />
                            <Text>Restaurant Location</Text>
                            <TextInput
                                style={styles.input}
                                onChangeText={setLocation}
                                value={location}
                                placeholder="South of Main St."
                                onSubmitEditing={() => {
                                    notesRef.current.focus();
                                }}
                                ref={locationRef}
                            />
                                <Text>Restaurant Notes</Text>
                                <TextInput
                                    style={styles.input}
                                    onChangeText={setNotes}
                                    value={notes}
                                    placeholder="Great dough!"
                                    onSubmitEditing={() => {
                                        createRestaurant();
                                    }}
                                    ref={notesRef}
                                />
                                <Button
                                    onPress={() => {
                                        createRestaurant();
                                    }}
                                    title="Update Restaurant"
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    accessibilityLabel="Update Restaurant"
                                    isLoading={isLoadingCreate}
                                />
                            </View>
                        </ScrollView>
                    </KeyboardAvoidingView>
                </View>
            </View>
        </SafeAreaView>

    )
}