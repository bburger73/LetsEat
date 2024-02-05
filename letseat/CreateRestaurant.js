import React from 'react';
import { useFocusEffect } from '@react-navigation/native';
import { KeyboardAvoidingView, BackHandler, SafeAreaView, StyleSheet, Text, View, TextInput, ScrollView, Platform } from "react-native";
import { useToast } from "react-native-toast-notifications";
import Button from './button';
import './global.js';

export default function CreateRestaurant(props) {
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


    const styles = StyleSheet.create(
        Platform.select({
            ios: global.mainstyles,
            android: global.mainstyles,
            default: global.mainstyles
        })
    );


    const [name, setName] = React.useState("");
    const [location, setLocation] = React.useState("");
    const [notes, setNotes] = React.useState("");

    
    const locationRef = React.useRef(null);
    const notesRef = React.useRef(null);
    
    const toast = useToast();
    const [isLoadingCreate, setLoadingCreate] = React.useState(false);


    const createRestaurant = () => {
        setLoadingCreate(true);
        if (name !== null && name !== undefined && name !== "") {
            const requestOptions = {
                method: "POST",
                headers: {
                    "LETSEAT-AUTHKEY": props.user.user_token,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    userId: props.user.id,
                    name: name,
                    location: location,
                    notes: notes
                }),
            };

            fetch(global.server + "restaurant/create.php", requestOptions).then(res => res.json()).then(result => {
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
                                    Create Restaurant
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
                                    title="Create Restaurant"
                                    // textstyle={styles.button}
                                    textstyle={styles.topbutton}
                                    accessibilityLabel="Create Restaurant"
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